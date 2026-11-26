<?php
declare(strict_types=1);

/**
 * Daftar isian yang bisa diedit di dashboard admin.
 *
 * path  : lokasi di data/content.php ("bagian" atau "bagian.kunci")
 * type  : text | textarea | url | email | wa | number | color | select | lines | tags | repeater
 * max   : panjang maksimal (teks) atau jumlah maksimal (lines / repeater)
 * half  : tampil setengah lebar
 */
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'schema.php') {
    http_response_code(404);
    exit;
}

$mockOptions = [
    'pack'  => 'Kemasan (pouch & box)',
    'brand' => 'Logo & identitas',
    'lumen' => 'UI aplikasi (Lumen)',
    'orbit' => 'UI dashboard (Orbit)',
    'vela'  => 'UI website (Vela)',
    'prism' => 'UI 3D (Prism)',
];

return [
    'panels' => [
        'profil' => [
            'title'  => 'Profil & kontak',
            'desc'   => 'Nama, profesi, dan cara klien menghubungimu.',
            'fields' => [
                ['path' => 'person.first', 'label' => 'Nama depan', 'type' => 'text', 'max' => 40, 'half' => true],
                ['path' => 'person.last', 'label' => 'Nama belakang', 'type' => 'text', 'max' => 40, 'half' => true],
                ['path' => 'person.role', 'label' => 'Profesi', 'type' => 'text', 'max' => 60, 'half' => true],
                ['path' => 'person.status', 'label' => 'Status', 'type' => 'text', 'max' => 60, 'half' => true, 'hint' => 'Mis. "Open for freelance projects"'],
                ['path' => 'person.city', 'label' => 'Kota (singkat)', 'type' => 'text', 'max' => 40, 'half' => true, 'hint' => 'Mis. "Karanganyar, ID"'],
                ['path' => 'person.location', 'label' => 'Lokasi lengkap', 'type' => 'text', 'max' => 100, 'half' => true],
                ['path' => 'person.email', 'label' => 'Email', 'type' => 'email', 'max' => 120, 'half' => true],
                ['path' => 'person.phone', 'label' => 'Nomor HP (tampilan)', 'type' => 'text', 'max' => 30, 'half' => true, 'hint' => 'Mis. "+62 812 3456 7890"'],
                ['path' => 'person.whatsapp', 'label' => 'Nomor WhatsApp', 'type' => 'wa', 'max' => 20, 'half' => true, 'hint' => 'Format 62xxx tanpa + dan spasi, mis. 6281234567890'],
                ['path' => 'social', 'label' => 'Media sosial', 'type' => 'repeater', 'max' => 8, 'item' => 'Link', 'fields' => [
                    ['key' => 'label', 'label' => 'Nama', 'type' => 'text', 'max' => 30, 'half' => true],
                    ['key' => 'href', 'label' => 'Link', 'type' => 'url', 'max' => 300, 'half' => true, 'hint' => 'https://instagram.com/… (isi # kalau belum ada)'],
                ]],
                ['path' => 'meta.title', 'label' => 'Judul di tab browser & Google', 'type' => 'text', 'max' => 90],
                ['path' => 'meta.description', 'label' => 'Deskripsi di hasil Google', 'type' => 'textarea', 'max' => 300],
            ],
        ],
        'hero' => [
            'title'  => 'Layar pembuka',
            'desc'   => 'Judul besar di bagian paling atas website.',
            'fields' => [
                ['path' => 'hero.lines', 'label' => 'Judul besar (satu kata/baris per baris)', 'type' => 'lines', 'max' => 8, 'itemMax' => 30, 'hint' => 'Tiap baris tampil berurutan. Kata terakhir diikuti kata yang berganti-ganti di bawah.'],
                ['path' => 'hero.rotate', 'label' => 'Kata yang berganti-ganti (satu per baris)', 'type' => 'lines', 'max' => 8, 'itemMax' => 30],
                ['path' => 'hero.intro', 'label' => 'Kalimat perkenalan', 'type' => 'textarea', 'max' => 300],
                ['path' => 'frame.pill', 'label' => 'Label kecil di poster', 'type' => 'text', 'max' => 40, 'half' => true],
                ['path' => 'frame.title', 'label' => 'Judul poster', 'type' => 'text', 'max' => 80, 'half' => true],
                ['path' => 'frame.left', 'label' => 'Teks pojok kiri poster', 'type' => 'text', 'max' => 40, 'half' => true],
                ['path' => 'frame.right', 'label' => 'Teks pojok kanan poster', 'type' => 'text', 'max' => 40, 'half' => true],
                ['path' => 'clients', 'label' => 'Nama klien/brand yang berjalan di bawah hero (satu per baris)', 'type' => 'lines', 'max' => 30, 'itemMax' => 40],
            ],
        ],
        'about' => [
            'title'  => 'Tentang saya',
            'desc'   => 'Cerita singkat, angka pencapaian, dan software yang kamu pakai.',
            'fields' => [
                ['path' => 'about.statement', 'label' => 'Kalimat utama', 'type' => 'textarea', 'max' => 400],
                ['path' => 'about.body', 'label' => 'Paragraf tambahan', 'type' => 'textarea', 'max' => 800],
                ['path' => 'about.stats', 'label' => 'Angka statistik', 'type' => 'repeater', 'max' => 6, 'item' => 'Angka', 'fields' => [
                    ['key' => 'value', 'label' => 'Angka', 'type' => 'number', 'third' => true],
                    ['key' => 'suffix', 'label' => 'Akhiran', 'type' => 'text', 'max' => 4, 'third' => true, 'hint' => 'Mis. + atau %'],
                    ['key' => 'label', 'label' => 'Keterangan', 'type' => 'text', 'max' => 50, 'third' => true],
                ]],
                ['path' => 'about.tools', 'label' => 'Software / tools (satu per baris)', 'type' => 'lines', 'max' => 20, 'itemMax' => 40],
                ['path' => 'archive.title', 'label' => 'Judul bagian galeri arsip', 'type' => 'text', 'max' => 60, 'half' => true],
                ['path' => 'archive.intro', 'label' => 'Keterangan bagian galeri arsip', 'type' => 'text', 'max' => 200, 'half' => true],
            ],
        ],
        'layanan' => [
            'title'  => 'Layanan & proses',
            'desc'   => 'Jasa yang kamu tawarkan dan tahapan kerja dengan klien.',
            'fields' => [
                ['path' => 'services', 'label' => 'Layanan', 'type' => 'repeater', 'max' => 10, 'item' => 'Layanan', 'fields' => [
                    ['key' => 'title', 'label' => 'Nama layanan', 'type' => 'text', 'max' => 50],
                    ['key' => 'desc', 'label' => 'Deskripsi', 'type' => 'textarea', 'max' => 300],
                    ['key' => 'chips', 'label' => 'Label kecil (pisahkan dengan koma)', 'type' => 'tags', 'max' => 6, 'itemMax' => 24],
                    ['key' => 'category', 'label' => 'Kategori portfolio yang dibuka (versi 3)', 'type' => 'text', 'max' => 40, 'list' => '@categories', 'hint' => 'Di versi 3, layanan ini tampil di bagian "Let’s Have a Look at My Portfolio" dan tombolnya membuka semua proyek dengan kategori ini. Kosongkan untuk dicocokkan otomatis dari judul layanan.'],
                ]],
                ['path' => 'process', 'label' => 'Tahapan kerja', 'type' => 'repeater', 'max' => 6, 'item' => 'Tahap', 'fields' => [
                    ['key' => 'title', 'label' => 'Nama tahap', 'type' => 'text', 'max' => 30, 'half' => true],
                    ['key' => 'time', 'label' => 'Waktu', 'type' => 'text', 'max' => 30, 'half' => true, 'hint' => 'Mis. "Hari 1–2"'],
                    ['key' => 'desc', 'label' => 'Deskripsi', 'type' => 'textarea', 'max' => 300],
                ]],
            ],
        ],
        'testimoni' => [
            'title'  => 'Testimoni',
            'desc'   => 'Kata-kata dari klien. Pakai testimoni asli, atau hapus semua kalau belum ada.',
            'fields' => [
                ['path' => 'testimonials', 'label' => 'Testimoni', 'type' => 'repeater', 'max' => 12, 'item' => 'Testimoni', 'fields' => [
                    ['key' => 'quote', 'label' => 'Isi testimoni', 'type' => 'textarea', 'max' => 400],
                    ['key' => 'name', 'label' => 'Nama', 'type' => 'text', 'max' => 50, 'half' => true],
                    ['key' => 'role', 'label' => 'Jabatan / usaha', 'type' => 'text', 'max' => 80, 'half' => true],
                ]],
            ],
        ],
        'v2' => [
            'title'  => 'Versi oranye (/v2/)',
            'desc'   => 'Teks yang hanya dipakai di desain kedua.',
            'fields' => [
                ['path' => 'v2.hello', 'label' => 'Sapaan', 'type' => 'text', 'max' => 20, 'half' => true],
                ['path' => 'v2.cta_accent', 'label' => 'Tombol ajakan', 'type' => 'text', 'max' => 40, 'half' => true],
                ['path' => 'v2.quote', 'label' => 'Kutipan di hero', 'type' => 'textarea', 'max' => 300],
                ['path' => 'v2.hire_title', 'label' => 'Judul "Why hire me"', 'type' => 'text', 'max' => 80],
                ['path' => 'v2.hire_text', 'label' => 'Teks "Why hire me"', 'type' => 'textarea', 'max' => 600],
                ['path' => 'v2.cta_title', 'label' => 'Judul ajakan di bawah', 'type' => 'text', 'max' => 80],
                ['path' => 'v2.ribbon', 'label' => 'Kata di pita berjalan (satu per baris)', 'type' => 'lines', 'max' => 16, 'itemMax' => 24],
                ['path' => 'experience', 'label' => 'Pengalaman kerja', 'type' => 'repeater', 'max' => 10, 'item' => 'Pengalaman', 'fields' => [
                    ['key' => 'place', 'label' => 'Tempat', 'type' => 'text', 'max' => 60, 'half' => true],
                    ['key' => 'where', 'label' => 'Kota', 'type' => 'text', 'max' => 60, 'half' => true],
                    ['key' => 'role', 'label' => 'Posisi', 'type' => 'text', 'max' => 60, 'half' => true],
                    ['key' => 'period', 'label' => 'Periode', 'type' => 'text', 'max' => 30, 'half' => true, 'hint' => 'Mis. "2022 — Now"'],
                    ['key' => 'desc', 'label' => 'Deskripsi', 'type' => 'textarea', 'max' => 300],
                ]],
            ],
        ],
        'v3' => [
            'title'  => 'Versi 3 (/v3/)',
            'desc'   => 'Teks khusus desain ketiga: pendidikan, paket harga, FAQ, dan jam kerja.',
            'fields' => [
                ['path' => 'v3.hello', 'label' => 'Sapaan di atas nama', 'type' => 'text', 'max' => 30, 'half' => true],
                ['path' => 'v3.rating', 'label' => 'Rating (mis. 4.9)', 'type' => 'text', 'max' => 4, 'half' => true],
                ['path' => 'v3.tagline', 'label' => 'Kalimat di bawah nama', 'type' => 'text', 'max' => 90],
                ['path' => 'v3.web3forms_key', 'label' => 'Access key Web3Forms (untuk versi online)', 'type' => 'text', 'max' => 64, 'hint' => 'Opsional. Daftar gratis di web3forms.com pakai email kamu, lalu tempel access key-nya di sini. Kalau kosong, form kontak di versi online akan membuka WhatsApp.'],
                ['path' => 'v3.quote', 'label' => 'Kutipan klien di hero', 'type' => 'textarea', 'max' => 220],
                ['path' => 'v3.hours', 'label' => 'Jam kerja', 'type' => 'repeater', 'max' => 4, 'item' => 'Jadwal', 'fields' => [
                    ['key' => '0', 'label' => 'Hari', 'type' => 'text', 'max' => 30, 'half' => true],
                    ['key' => '1', 'label' => 'Jam', 'type' => 'text', 'max' => 30, 'half' => true],
                ]],
                ['path' => 'education', 'label' => 'Pendidikan', 'type' => 'repeater', 'max' => 8, 'item' => 'Pendidikan', 'fields' => [
                    ['key' => 'place', 'label' => 'Sekolah / kursus', 'type' => 'text', 'max' => 60, 'third' => true],
                    ['key' => 'title', 'label' => 'Jurusan', 'type' => 'text', 'max' => 60, 'third' => true],
                    ['key' => 'period', 'label' => 'Tahun', 'type' => 'text', 'max' => 30, 'third' => true],
                ]],
                ['path' => 'faq', 'label' => 'Pertanyaan yang sering ditanya (FAQ)', 'type' => 'repeater', 'max' => 12, 'item' => 'Pertanyaan', 'fields' => [
                    ['key' => 'q', 'label' => 'Pertanyaan', 'type' => 'text', 'max' => 120],
                    ['key' => 'a', 'label' => 'Jawaban', 'type' => 'textarea', 'max' => 500],
                ]],
            ],
        ],
    ],

    // Isian satu proyek (bagian "Proyek")
    'project' => [
        ['key' => 'title', 'label' => 'Judul proyek', 'type' => 'text', 'max' => 80, 'half' => true],
        ['key' => 'client', 'label' => 'Nama klien / brand', 'type' => 'text', 'max' => 60, 'half' => true],
        ['key' => 'category', 'label' => 'Kategori', 'type' => 'text', 'max' => 40, 'third' => true, 'hint' => 'Dipakai sebagai filter di /v2/', 'list' => ['Packaging', 'Logo & Brand identity', 'Print', 'Social media', 'UI/UX']],
        ['key' => 'role', 'label' => 'Peranmu', 'type' => 'text', 'max' => 60, 'third' => true, 'hint' => 'Mis. "Packaging · Logo"'],
        ['key' => 'year', 'label' => 'Tahun', 'type' => 'text', 'max' => 10, 'third' => true],
        ['key' => 'featured', 'label' => 'Tampil di beranda /v3/?', 'type' => 'select', 'options' => ['' => 'Tidak, hanya di halaman semua proyek', '1' => 'Ya, jadikan proyek pilihan'], 'hint' => 'Beranda /v3/ menampilkan maksimal 3 proyek pilihan. Kalau belum ada yang dipilih, 3 proyek teratas yang tampil.'],
        ['key' => 'summary', 'label' => 'Cerita singkat proyek', 'type' => 'textarea', 'max' => 500, 'hint' => 'Tampil di kartu proyek dan di bawah judul halaman detail.'],
        ['key' => 'body', 'label' => 'Cerita lengkap (halaman detail, opsional)', 'type' => 'textarea', 'max' => 4000, 'hint' => 'Tantangan, proses, dan hasil. Pisahkan paragraf dengan satu baris kosong.'],
        ['key' => 'tags', 'label' => 'Tag (pisahkan dengan koma)', 'type' => 'tags', 'max' => 6, 'itemMax' => 24, 'half' => true],
        ['key' => 'href', 'label' => 'Link Behance / Instagram (opsional)', 'type' => 'url', 'max' => 300, 'half' => true, 'hint' => 'Kalau diisi, muncul tombol "View project".'],
        ['key' => 'metrics', 'label' => 'Hasil / angka', 'type' => 'repeater', 'max' => 3, 'item' => 'Hasil', 'fields' => [
            ['key' => '0', 'label' => 'Angka', 'type' => 'text', 'max' => 12, 'half' => true, 'hint' => 'Mis. "+60%"'],
            ['key' => '1', 'label' => 'Keterangan', 'type' => 'text', 'max' => 40, 'half' => true, 'hint' => 'Mis. "penjualan"'],
        ]],
        ['key' => 'mock', 'label' => 'Mockup cadangan (tampil selama belum ada gambar)', 'type' => 'select', 'options' => $mockOptions, 'group' => 'mock'],
        ['key' => 'pack.brand', 'label' => 'Nama di kemasan', 'type' => 'text', 'max' => 30, 'third' => true, 'show' => ['mock', 'pack'], 'group' => 'mock'],
        ['key' => 'pack.product', 'label' => 'Jenis produk', 'type' => 'text', 'max' => 30, 'third' => true, 'show' => ['mock', 'pack'], 'group' => 'mock'],
        ['key' => 'pack.size', 'label' => 'Berat / isi', 'type' => 'text', 'max' => 12, 'third' => true, 'show' => ['mock', 'pack'], 'group' => 'mock'],
        ['key' => 'pack.c1', 'label' => 'Warna utama', 'type' => 'color', 'third' => true, 'show' => ['mock', 'pack'], 'group' => 'mock', 'default' => '#3b22f0'],
        ['key' => 'pack.c2', 'label' => 'Warna kedua', 'type' => 'color', 'third' => true, 'show' => ['mock', 'pack'], 'group' => 'mock', 'default' => '#d6d1ff'],
        ['key' => 'pack.c3', 'label' => 'Warna latar', 'type' => 'color', 'third' => true, 'show' => ['mock', 'pack'], 'group' => 'mock', 'default' => '#f4f3fb'],
        ['key' => 'brand.name', 'label' => 'Nama brand', 'type' => 'text', 'max' => 30, 'half' => true, 'show' => ['mock', 'brand'], 'group' => 'mock'],
        ['key' => 'brand.tagline', 'label' => 'Tagline', 'type' => 'text', 'max' => 40, 'half' => true, 'show' => ['mock', 'brand'], 'group' => 'mock'],
        ['key' => 'brand.c1', 'label' => 'Warna gelap', 'type' => 'color', 'quarter' => true, 'show' => ['mock', 'brand'], 'group' => 'mock', 'default' => '#160a6e'],
        ['key' => 'brand.c2', 'label' => 'Warna aksen', 'type' => 'color', 'quarter' => true, 'show' => ['mock', 'brand'], 'group' => 'mock', 'default' => '#8a7bff'],
        ['key' => 'brand.c3', 'label' => 'Warna terang', 'type' => 'color', 'quarter' => true, 'show' => ['mock', 'brand'], 'group' => 'mock', 'default' => '#f4f3fb'],
        ['key' => 'brand.c4', 'label' => 'Warna tambahan', 'type' => 'color', 'quarter' => true, 'show' => ['mock', 'brand'], 'group' => 'mock', 'default' => '#ff8fb7'],
    ],
];
