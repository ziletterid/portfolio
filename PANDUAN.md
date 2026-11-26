# Panduan Edit Portfolio

## Cara termudah: dashboard admin

Buka **http://localhost:8080/admin/**

**Pertama kali:** buat password (minimal 8 karakter). Pembuatan password hanya bisa dilakukan dari komputermu sendiri.

Menu di samping kiri:

| Menu | Isinya |
|---|---|
| **Beranda** | Ringkasan dan daftar data contoh yang masih perlu diganti |
| **Profil & kontak** | Foto profil, nama, profesi, email, WhatsApp, media sosial, judul di Google |
| **Layar pembuka** | Judul besar hero, kata yang berganti-ganti, poster, daftar klien |
| **Proyek** | Tambah, edit, urutkan, dan hapus proyek beserta gambarnya |
| **Galeri arsip** | Logo, label, poster, dll. dengan keterangan |
| **Tentang saya** | Cerita, angka statistik, software |
| **Layanan & proses** | Jasa yang ditawarkan dan tahapan kerja |
| **Testimoni** | Kata-kata klien |
| **Pesan masuk** | Pesan dari form kontak website |
| **Versi oranye** | Teks khusus desain kedua (/v2/) |
| **Versi 3** | Khusus desain ketiga (/v3/): pendidikan, FAQ, jam kerja, rating |

**Halaman proyek di /v3/:** beranda hanya menampilkan kartu per kategori dan 3 proyek pilihan. Klik kartu kategori atau **View all projects** untuk membuka daftar semua proyek (ada filter dan pencarian). Klik satu proyek untuk membuka halaman detailnya: cover besar, cerita lengkap, info klien, semua gambar, dan tombol ke proyek sebelum/berikutnya. Di dashboard, pilih **Tampil di beranda = Ya** untuk memilih proyek yang tampil di beranda, dan isi **Cerita lengkap** untuk halaman detail.

**Menambah desain baru:** buka **Proyek**, ketik judul, klik **+ Tambah proyek**. Upload gambarnya (gambar pertama jadi cover), isi ceritanya, lalu klik **Simpan perubahan**.

Tips:
- Bar di bawah menunjukkan apakah ada perubahan yang belum disimpan. Bisa juga simpan dengan Ctrl/Cmd + S.
- **Reset ke bawaan** mengembalikan satu bagian ke teks asli di `data/content.php`.
- Gambar yang lebih besar dari 2400 px otomatis dikecilkan. Gambar dan proyek yang dihapus dipindah ke `storage/trash/`, jadi masih bisa dikembalikan.
- Semua perubahan dari dashboard disimpan di `storage/content.json`. **Ikut salin file ini** saat memindahkan website ke hosting.
- Kalau membuka dashboard dari browser lain (atau mode HP di browser), kamu perlu login lagi. Ini disengaja untuk keamanan.

Lupa password? Hapus file `storage/admin.json`, lalu buka `/admin/` lagi untuk membuat password baru.

> Cara manual di bawah tetap berfungsi. Kalau satu bagian sudah pernah disimpan dari dashboard, isi dashboard yang dipakai (menimpa `data/content.php`). Klik **Reset ke bawaan** kalau ingin kembali memakai isi file.

---

## Cara manual

Ada dua hal yang bisa kamu ubah: **satu file teks** dan **folder gambar**.

```
portfolio/
├── data/content.php          ← SEMUA TEKS: profil, proyek, layanan, testimoni, kontak
└── assets/img/
    ├── profile.jpg           ← FOTO PROFIL (buat sendiri, nama harus "profile")
    ├── work/                 ← GAMBAR PROYEK UTAMA (1 folder per proyek)
    │   ├── kopi-lereng-lawu/
    │   ├── teh-tawangmangu/
    │   ├── keripik-mbok-sri/
    │   └── lumen-app/
    └── gallery/              ← ARSIP: logo, label, poster, feed IG, dll.
```

Setelah mengubah apa pun: **simpan → refresh browser** (http://localhost:8080).

---

## 1. Edit profil

Buka `data/content.php`, cari bagian **`2. PROFIL`**:

```php
'first'    => 'Dzakaria',
'last'     => 'Azizi',
'role'     => 'Graphic Designer',
'email'    => 'email@kamu.com',        // ganti
'phone'    => '+62 812 0000 0000',     // ganti
'whatsapp' => '6281200000000',         // ganti (62xxx, tanpa + dan spasi)
```

- **Foto profil:** taruh fotomu di `assets/img/` dengan nama `profile.jpg` (atau `.png` / `.webp`). Foto otomatis muncul di kartu About. Pakai foto persegi minimal 800×800 px.
- **Teks About, angka statistik, dan tools:** bagian **`6. ABOUT`**.
- **Link Instagram/Behance:** bagian `social` di paling bawah file.

## 2. Tambah atau ganti proyek utama (section "Work")

Setiap proyek punya **slug**, yaitu nama folder gambarnya.

1. Buat folder `assets/img/work/<slug>/`, misalnya `assets/img/work/kopi-senja/`.
2. Masukkan gambar dan beri nama berurutan: `01.jpg`, `02.jpg`, `03.jpg`, …
   - `01` = gambar besar (pakai foto/mockup terbaik)
   - `02` dan `03` = dua gambar kecil di sebelahnya
   - gambar ke-4 dan seterusnya bisa dilihat saat gambar diklik (lightbox)
3. Di `data/content.php` bagian **`4. PROYEK UTAMA`**, ubah atau salin satu blok proyek:

```php
[
    'slug'    => 'kopi-senja',               // = nama folder gambar
    'client'  => 'Kopi Senja',
    'title'   => 'Kopi Senja Rebrand',
    'category'=> 'Packaging',
    'summary' => 'Cerita singkat proyeknya (1–2 kalimat).',
    'role'    => 'Packaging · Logo',
    'year'    => '2026',
    'tags'    => ['Packaging', 'Logo'],
    'metrics' => [['+40%', 'penjualan'], ['3', 'varian rasa']],
    'href'    => '',                         // link Behance/IG (opsional)
    'mock'    => 'pack',
],
```

Selama folder gambarnya masih kosong, situs menampilkan mockup bawaan (`'mock' => 'pack'` untuk kemasan, `'brand'` untuk logo). Begitu ada gambar, mockup otomatis diganti gambarmu.

Kalau `href` diisi link (Behance, Instagram, dll.), muncul tombol **View project**.

## 3. Isi galeri arsip (logo, label, poster, dll.)

Taruh gambar di `assets/img/gallery/`. Gambar langsung tampil tanpa perlu mengedit file apa pun.

Nama file dipakai sebagai keterangan:

| Nama file | Tampil sebagai |
|---|---|
| `01-logo-kopi-senja.png` | Logo Kopi Senja |
| `02-label-madu-lawu.jpg` | Label Madu Lawu |
| `03-poster-festival.webp` | Poster Festival |

Angka di depan hanya untuk mengatur urutan dan tidak ikut tampil.

## 4. Tips gambar

- Format: **JPG/WebP** untuk foto/mockup, **PNG/SVG** untuk logo.
- Ukuran: lebar sekitar **1600–2000 px**, dan usahakan di bawah **500 KB** per gambar (kompres di squoosh.app atau tinypng.com).
- Untuk kemasan, mockup foto realistis jauh lebih menarik daripada desain flat.

## 5. Yang masih contoh, ganti dengan data aslimu

- [ ] Email, nomor HP, WhatsApp (`2. PROFIL`)
- [ ] 4 proyek contoh (`4. PROYEK UTAMA`): Lereng Lawu, Tawangmangu, Mbok Sri, Lumen
- [ ] Daftar klien (`clients`)
- [ ] Angka statistik (`6. ABOUT → stats`)
- [ ] Testimoni (`testimonials`): pakai testimoni asli atau hapus
- [ ] Link media sosial (`social`)

## 6. Menjalankan di komputer

```bash
cd "/Users/zichie/Projek PHP/portfolio"
php -S localhost:8080 router.php
```

Lalu buka http://localhost:8080 (versi indigo), http://localhost:8080/v2/ (versi oranye), http://localhost:8080/v3/ (versi 3, putih-oranye), atau http://localhost:8080/admin/ (dashboard). `router.php` mengunci folder privat (`storage/`, `data/`, `inc/`) supaya tidak bisa dibuka dari browser. Form kontak menyimpan pesan ke `storage/messages.jsonl`. Pengiriman ke email baru aktif setelah situs dipasang di hosting yang mendukung email.

## Online gratis lewat GitHub Pages (versi statis)

Website di internet adalah **salinan statis** dari desain /v3/. Dashboard tetap dipakai di laptop.

1. Jalankan server lokal, lalu edit isi website lewat `http://localhost:8080/admin/`.
2. Buat versi statis:
   ```
   php tools/export.php
   ```
   Hasilnya ada di folder `docs/` (halaman utama, daftar proyek, satu halaman per proyek, dan 404).
3. Upload ke GitHub:
   ```
   git add -A
   git commit -m "Update portfolio"
   git push
   ```
4. Sekali saja: di GitHub buka repo → **Settings → Pages** → Source: **Deploy from a branch** → Branch **main**, folder **/docs** → Save.
   Website tampil di `https://ziletterid.github.io/portfolio/` setelah ±1 menit.

**Catatan**
- Folder `storage/` (password dan data dashboard) tidak ikut ter-upload (ada di `.gitignore`).
- Form kontak di versi online membuka WhatsApp (atau email kalau nomor WhatsApp kosong).
  Supaya pesan langsung masuk ke email, daftar gratis di web3forms.com, lalu tempel access key-nya di
  **Dashboard → Versi 3 → Access key Web3Forms**, kemudian export ulang.
- GitHub Pages gratis hanya untuk repo **publik**. Kalau repo tetap privat, folder `docs/` bisa di-deploy gratis
  ke Vercel atau Cloudflare Pages (Output directory: `docs`).
