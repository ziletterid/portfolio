<?php
declare(strict_types=1);

/**
 * Handler form kontak.
 * - Validasi server-side, honeypot, rate limit sederhana per IP (3 pesan / 10 menit).
 * - Pesan selalu disimpan ke storage/messages.jsonl, lalu dicoba dikirim lewat mail().
 *   Ganti bagian mail() dengan PHPMailer + SMTP saat dipasang di hosting.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $code, array $body): never
{
    http_response_code($code);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed.']);
}

require __DIR__ . '/../inc/helpers.php';
$c       = site_content();
$storage = __DIR__ . '/../storage';

// Honeypot: bot mengisi field tersembunyi → pura-pura sukses
if (!empty($_POST['website'])) {
    respond(200, ['ok' => true, 'message' => 'Thanks!']);
}

// Rate limit per IP
$ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rlFile  = $storage . '/ratelimit.json';
$now     = time();
$rl      = is_file($rlFile) ? (json_decode((string)file_get_contents($rlFile), true) ?: []) : [];
$key     = hash('sha256', $ip);
$hits    = array_values(array_filter($rl[$key] ?? [], fn($t) => $t > $now - 600));
if (count($hits) >= 3) {
    respond(429, ['ok' => false, 'error' => 'Too many messages. Please try again in a few minutes.']);
}

$name    = trim((string)($_POST['name'] ?? ''));
$email   = trim((string)($_POST['email'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$budget  = trim((string)($_POST['budget'] ?? ''));
$topics  = array_values(array_filter(array_map('strval', (array)($_POST['topics'] ?? []))));
$phone   = trim((string)($_POST['phone'] ?? ''));
$deadline = trim((string)($_POST['deadline'] ?? ''));

$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 120)            $errors['name'] = 'Please tell me your name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))                 $errors['email'] = 'That email does not look right.';
if (mb_strlen($message) < 10 || mb_strlen($message) > 4000)     $errors['message'] = 'A sentence or two about the project, please.';
if ($errors) {
    respond(422, ['ok' => false, 'error' => reset($errors), 'fields' => $errors]);
}

$entry = [
    'at'      => date('c'),
    'name'    => $name,
    'email'   => $email,
    'phone'   => mb_substr(preg_replace('/[^\d+ ()-]/', '', $phone) ?? '', 0, 30),
    'budget'  => mb_substr($budget, 0, 40),
    'deadline'=> mb_substr($deadline, 0, 40),
    'topics'  => array_slice(array_map(fn($t) => mb_substr($t, 0, 40), $topics), 0, 6),
    'message' => $message,
];

if (!is_dir($storage)) {
    @mkdir($storage, 0750, true);
}
$saved = @file_put_contents($storage . '/messages.jsonl', json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
if ($saved === false) {
    respond(500, ['ok' => false, 'error' => 'Could not send right now. Please email me directly.']);
}

$hits[]   = $now;
$rl[$key] = $hits;
@file_put_contents($rlFile, json_encode($rl), LOCK_EX);

// Kirim email (bisa gagal di lokal; pesan tetap tersimpan)
$to      = $c['person']['email'];
$subject = 'New project inquiry from ' . preg_replace('/[\r\n]+/', ' ', $name);
$body    = "Name: {$name}\nEmail: {$email}\nPhone: {$entry['phone']}\nDeadline: {$entry['deadline']}\nBudget: {$entry['budget']}\nTopics: " . implode(', ', $entry['topics']) . "\n\n{$message}\n";
$headers = "From: Portfolio <no-reply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . ">\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=utf-8";
@mail($to, $subject, $body, $headers);

respond(200, ['ok' => true, 'message' => 'Thanks, ' . explode(' ', $name)[0] . '! I will reply within one working day.']);
