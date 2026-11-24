# PRD — Website Portfolio Personal (Gaya 3D Bold)

| | |
|---|---|
| **Versi** | 1.0 (draft) |
| **Tanggal** | 24 November 2026 |
| **Referensi** | Video `c17e9cbf71810b4ac4ba1673efb70d19_720w.mp4` (portfolio "Hi, I'm Alex", dibuat di Wix Studio) |
| **Status** | Menunggu konfirmasi konten & stack |

---

## 1. Ringkasan

Website portfolio satu halaman (one-page, scroll panjang) untuk seorang kreator/desainer. Gayanya **bold dan playful**: latar hitam pekat, tipografi display super tebal, karakter 3D sebagai maskot di hero, objek 3D mengambang (bunga, hati, kubus, bentuk "jelly"), aksen gradien ungu–pink, dan animasi scroll yang halus. Ada satu section terang (Services & Contact) untuk memberi ritme kontras.

**Tujuan utama:** membuat calon klien terkesan dalam 5 detik pertama, lalu mengarahkan mereka ke tombol **Contact Me**.

## 2. Tujuan & Ukuran Keberhasilan

| Tujuan | Metrik |
|---|---|
| Kesan pertama kuat | Hero tampil penuh < 2,5 detik (LCP) |
| Mendorong kontak | Klik "Contact Me" + form terkirim |
| Menampilkan karya | Pengunjung scroll sampai section Projects ≥ 60% |
| Terlihat profesional di semua layar | Lolos uji di 375px, 768px, 1440px |
| Performa | Lighthouse Performance ≥ 85, Accessibility ≥ 90 |

## 3. Target Pengguna

1. **Calon klien** (pemilik bisnis, brand, agensi) — ingin melihat kualitas karya dan cara menghubungi.
2. **Rekruter / HRD** — ingin tahu keahlian dan pengalaman dengan cepat.
3. **Sesama kreator** — melihat gaya dan referensi.

## 4. Arah Visual (Design Direction)

### 4.1 Palet warna
| Token | Nilai (perkiraan) | Pemakaian |
|---|---|---|
| `--bg-dark` | `#0A0A0A` | Latar utama |
| `--bg-light` | `#E9EDF0` | Section Services & Contact |
| `--text-light` | `#D9DDE0` (abu metalik) | Judul display di latar gelap |
| `--text-dark` | `#111111` | Teks di latar terang |
| `--accent-gradient` | `linear-gradient(90deg, #5B2BFF, #C13BFF, #FF3D8B)` | Tombol CTA, logo footer |
| `--border` | `rgba(255,255,255,0.35)` | Outline kartu proyek & testimoni |

### 4.2 Tipografi
- **Display (judul besar):** sans-serif extra-bold/black, huruf rapat, ALL CAPS. Contoh pilihan: *Archivo Black*, *Anton*, atau *Bricolage Grotesque ExtraBold*. Pada judul section diberi efek **outline/teks berongga** yang lalu terisi saat muncul.
- **Body:** grotesk bersih, ukuran 14–16px. Contoh: *Inter* atau *Space Grotesk*.
- **Angka layanan (01, 02…):** display font, sangat tebal.

### 4.3 Aset 3D
- Karakter 3D (render gaya kartun) — foto/render diri pemilik atau avatar.
- Objek dekoratif: bunga ungu, hati merah, kubus biru, bentuk "chrome/jelly", emoji 3D (😍, 😲), petir hijau-kuning.
- Format: **PNG/WebP transparan** (wajib) — opsional dikembangkan ke model `.glb` dengan Three.js pada fase 2.

### 4.4 Gaya umum
- Sudut membulat besar (radius 24–32px) pada kartu.
- Kartu dengan **border tipis berlapis** (efek garis ganda) di Projects & Testimonials.
- Banyak ruang kosong; teks body rata tengah di About.

## 5. Struktur Halaman & Kebutuhan Fungsional

### 5.0 Navigasi (Header)
- Menu teks kecil ALL CAPS, tersebar merata: **About · Customers · Projects · Contact**.
- Klik menu → smooth scroll ke section.
- Mobile: berubah jadi tombol hamburger + menu layar penuh.
- **Kriteria:** header tetap terbaca di atas hero; transparan, tanpa latar.

### 5.1 Hero
- Judul raksasa **"HI, I'M [NAMA]"** selebar layar.
- Karakter 3D berada **di depan sebagian teks** (efek berlapis: sebagian huruf tertutup kepala karakter).
- Kiri bawah: tagline 2–3 baris ALL CAPS, contoh *"A 3D designer passionate about crafting bold and memorable projects 😎"*.
- Kanan: tombol **CONTACT ME** berbentuk pil dengan border gradien + panah.
- **Animasi:** judul masuk dari bawah per huruf/kata; karakter sedikit mengikuti posisi kursor (parallax ringan); saat scroll, karakter naik lebih lambat dari teks.

### 5.2 Logo Klien (Customers)
- Baris logo klien/brand putih monokrom (6–8 logo).
- **Animasi:** marquee berjalan otomatis (infinite loop), berhenti saat di-hover.

### 5.3 Galeri Karya (Showcase Grid)
- Grid gambar render 3D berwarna cerah dengan sudut membulat, 2 baris × 4–5 kolom.
- **Animasi:** tiap baris bergeser horizontal berlawanan arah saat scroll (scroll-linked parallax).
- Hover: gambar sedikit membesar (scale 1.03).

### 5.4 About Me
- Judul **"ABOUT ME"** besar, abu metalik, muncul dari efek outline → terisi.
- Paragraf 4–5 baris rata tengah (pengalaman, spesialisasi, ajakan kolaborasi).
- Tombol **CONTACT ME** gradien.
- Objek 3D mengambang di sekitar judul (bunga, hati, kubus, bentuk chrome).
- **Animasi:** objek 3D melayang naik-turun pelan (float loop) dan bergerak parallax saat scroll; paragraf muncul baris per baris (reveal).

### 5.5 Services
- **Latar berubah jadi terang** (transisi warna saat section masuk viewport).
- Judul **"SERVICES"**.
- Daftar bernomor besar dengan garis pemisah:
  - **01** — 3D Modeling
  - **02** — 3D Rendering / Lighting
  - **03** — 3D Animation
  - **04** — Product Design
  - **05** — (opsional)
- Tiap item: nomor besar kiri, nama layanan + deskripsi 1–2 baris kanan.
- **Animasi:** tiap baris muncul dari bawah berurutan; garis pemisah "menggambar" dari kiri ke kanan.

### 5.6 Projects
- Kembali ke latar gelap. Judul **"PROJECTS"**.
- Kartu proyek bergaya **sticky stack**: saat scroll, kartu 01 menempel di atas, kartu 02 menumpuk di bawahnya, dst. (kartu sebelumnya tetap terlihat sebagian sebagai "tab").
- Isi kartu:
  - Kiri atas: nomor (**01**), label "CLIENT", nama klien.
  - Kanan atas: tombol pil outline **LIVE PROJECT** (link eksternal, buka tab baru).
  - Bawah: 2 gambar (1 besar + 1 kecil) bersudut membulat.
- Minimal 3, maksimal 6 proyek.

### 5.7 Testimonials — "What Clients Are Saying"
- Judul 2 baris rata tengah + emoji 3D 😍 di samping.
- Grid kartu testimoni (2 baris, 3 kolom terlihat): kutipan, foto bulat, nama, jabatan & perusahaan.
- **Animasi:** baris bergeser horizontal (marquee lambat, dua baris berlawanan arah); judul memakai efek reveal.

### 5.8 Contact — "LET'S GET IN TOUCH"
- Latar terang. Judul besar 3 baris kiri + email klikabel (`mailto:`) bergaris bawah.
- Kanan: form **Nama, Email, Pesan** + tombol **SEND** (pil outline ungu).
- Objek 3D dekoratif (bentuk ungu, petir hijau).
- **Fungsional:**
  - Validasi di sisi browser (wajib diisi, format email).
  - Dikirim ke backend PHP → diteruskan ke email pemilik.
  - Proteksi spam: honeypot field + rate limit sederhana.
  - Pesan sukses/gagal tampil tanpa reload halaman.

### 5.9 Footer
- Kartu hitam besar bersudut membulat yang "muncul" menutupi section Contact.
- Kiri: nama besar (**[NAMA DEPAN] [NAMA BELAKANG]**) abu metalik.
- Kanan: kolom **SOCIAL** (Instagram, Facebook, Behance, LinkedIn…) dan **CONTACT** (email, telepon, alamat).
- Bawah: **nama/logo raksasa selebar footer** bergaya huruf 3D berwarna gradien (ungu–pink–putih).
- Copyright © tahun.

## 6. Kebutuhan Animasi & Interaksi (Ringkasan)

| ID | Efek | Section | Prioritas |
|---|---|---|---|
| A1 | Smooth scroll (inertia) | Global | Wajib |
| A2 | Teks reveal dari bawah / outline → isi | Hero, About, Services, Projects, Testimonials | Wajib |
| A3 | Marquee logo & testimoni | Customers, Testimonials | Wajib |
| A4 | Parallax horizontal grid gambar | Showcase | Wajib |
| A5 | Float loop objek 3D | About, Contact | Wajib |
| A6 | Transisi warna latar gelap ↔ terang | Services, Contact | Wajib |
| A7 | Sticky stacking cards | Projects | Wajib |
| A8 | Karakter mengikuti kursor | Hero | Nice-to-have |
| A9 | Footer reveal (menimpa section sebelumnya) | Footer | Nice-to-have |
| A10 | Model 3D interaktif (Three.js) | Hero | Fase 2 |

**Aturan:** semua animasi menghormati `prefers-reduced-motion` (dimatikan/dipersingkat) dan hanya memakai `transform`/`opacity` agar tetap 60 fps.

## 7. Kebutuhan Non-Fungsional

- **Responsif:** desktop (≥1280), tablet (768–1279), mobile (≤767). Di mobile, grid jadi 1–2 kolom, judul tetap besar tapi tidak meluber, objek 3D dikurangi.
- **Performa:** gambar WebP/AVIF + `loading="lazy"` (kecuali hero), font di-*preload*, total halaman awal < 2 MB.
- **SEO:** title & meta description, Open Graph image, satu `<h1>` (di hero), heading berurutan, alt text di semua gambar.
- **Aksesibilitas:** kontras teks body ≥ 4.5:1, fokus keyboard terlihat, tombol & link punya label jelas.
- **Browser:** Chrome, Safari, Firefox, Edge versi terbaru; Safari iOS.
- **Konten mudah diubah:** semua teks, proyek, testimoni, dan link disimpan di satu file data (mis. `data/content.php` atau `content.json`), bukan tersebar di HTML.

## 8. Usulan Teknologi

| Lapisan | Pilihan | Alasan |
|---|---|---|
| Markup | PHP (template + include per section) | Sesuai folder proyek, mudah di-hosting di shared hosting |
| Styling | CSS modern (variabel, clamp, grid) atau Tailwind CSS | Cepat dan konsisten |
| Animasi | **GSAP + ScrollTrigger**, **Lenis** (smooth scroll) | Standar industri untuk efek scroll seperti di video |
| Form | PHP + PHPMailer (SMTP) | Pengiriman email andal |
| 3D (fase 2) | Three.js / `<model-viewer>` | Model `.glb` interaktif |
| Hosting | Shared hosting PHP / VPS | — |

Alternatif kalau tidak butuh backend: HTML statis + form lewat Formspree, di-deploy ke Netlify/Vercel.

## 9. Struktur Folder (Usulan)

```
portfolio/
├── index.php
├── partials/        # header, hero, about, services, projects, testimonials, contact, footer
├── data/content.php # semua teks & data proyek
├── api/contact.php  # handler form
├── assets/
│   ├── css/
│   ├── js/          # main.js, animations.js
│   ├── img/         # render 3D, proyek, avatar testimoni
│   └── fonts/
└── PRD.md
```

## 10. Konten yang Perlu Disiapkan Pemilik

- [ ] Nama lengkap & tagline
- [ ] Karakter/avatar 3D (PNG transparan, ±1500px)
- [ ] 6–8 logo klien (SVG putih)
- [ ] 8–10 gambar karya untuk showcase grid
- [ ] Paragraf About Me (3–5 kalimat)
- [ ] 4–5 layanan + deskripsi singkat
- [ ] 3–6 proyek: nama klien, 2 gambar, link live
- [ ] 4–6 testimoni: kutipan, nama, jabatan, perusahaan, foto
- [ ] Email, telepon, alamat, link media sosial
- [ ] 6–10 objek 3D dekoratif (PNG transparan)

## 11. Tahapan Pengerjaan

| Fase | Lingkup | Estimasi |
|---|---|---|
| 1. Fondasi | Struktur folder, design token, font, layout statis semua section | 2–3 hari |
| 2. Responsif | Penyesuaian tablet & mobile | 1–2 hari |
| 3. Animasi | A1–A7 dengan GSAP + Lenis | 2–3 hari |
| 4. Form & backend | `api/contact.php`, validasi, anti-spam | 1 hari |
| 5. Polish & QA | Performa, aksesibilitas, SEO, uji lintas browser | 1–2 hari |
| 6. (Opsional) | A8–A10, model 3D interaktif, multi-bahasa | — |

## 12. Di Luar Lingkup (v1)

- CMS / panel admin
- Halaman detail per proyek (case study)
- Blog
- Multi-bahasa
- Dark/light mode toggle

## 13. Pertanyaan Terbuka

1. Stack final: **PHP + GSAP** atau **HTML statis + Formspree**?
2. Profesi/fokus pemilik — tetap "3D designer" atau bidang lain (web developer, UI/UX, fotografer)? Ini menentukan isi Services.
3. Sudah punya karakter 3D sendiri, atau perlu dibuat (AI render / commission)?
4. Bahasa konten: Indonesia, Inggris, atau keduanya?
5. Domain & hosting yang akan dipakai?

---

*Catatan: referensi di video adalah template "Alex Turner" yang dibangun di Wix Studio. Implementasi ini hanya mengikuti struktur dan gayanya; semua nama, teks, gambar, dan logo diganti dengan milik pemilik sendiri.*
