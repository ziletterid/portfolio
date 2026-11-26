<?php
/**
 * ============================================================
 *  FILE KONTEN UTAMA — semua teks website ada di sini.
 *  Ubah teks di antara tanda kutip '...', simpan, lalu refresh browser.
 *  Panduan lengkap: lihat PANDUAN.md di folder proyek.
 * ============================================================
 */
return [

    // ------------------------------------------------------------
    // 1. INFO HALAMAN (judul tab browser & deskripsi di Google)
    // ------------------------------------------------------------
    'meta' => [
        'title'       => 'Dzakaria Azizi — Graphic Designer',
        'description' => 'Portfolio Dzakaria Azizi, graphic designer dari Karanganyar: desain kemasan, logo & identitas brand, dan UI/UX.',
        'url'         => 'https://example.com',
    ],

    // ------------------------------------------------------------
    // 2. PROFIL
    //    Foto profil: taruh file bernama profile.jpg (atau .png/.webp)
    //    di folder assets/img/ — otomatis tampil di kartu About.
    // ------------------------------------------------------------
    'person' => [
        'first'    => 'Dzakaria',
        'last'     => 'Azizi',
        'role'     => 'Graphic Designer',
        'city'     => 'Karanganyar, ID',
        'location' => 'Karanganyar, Central Java, Indonesia',
        'timezone' => 'Asia/Jakarta',
        'email'    => 'email@kamu.com',            // GANTI dengan email aslimu
        'phone'    => '+62 812 0000 0000',         // GANTI dengan nomor aslimu
        'whatsapp' => '6281200000000',             // GANTI: format 62xxx tanpa + dan spasi (untuk tombol WhatsApp)
        'status'   => 'Open for freelance projects',
    ],

    'nav' => [
        ['id' => 'work',     'label' => 'Work'],
        ['id' => 'archive',  'label' => 'Archive'],
        ['id' => 'about',    'label' => 'About'],
        ['id' => 'services', 'label' => 'Services'],
        ['id' => 'contact',  'label' => 'Contact'],
    ],

    // ------------------------------------------------------------
    // 3. HERO (layar pertama)
    // ------------------------------------------------------------
    'hero' => [
        'lines'  => ['Beyond', 'logos,', 'beyond', 'packaging,', 'beyond'],
        'rotate' => ['the shelf', 'the brand', 'the ordinary', 'the screen'],   // kata terakhir yang berganti-ganti
        'intro'  => 'Graphic designer crafting packaging, logos and brand identities that stand out on the shelf and on the screen.',
    ],

    'frame' => [
        'pill'  => 'Selected work',
        'title' => 'Brands people remember at first glance.',
        'left'  => 'BY DZAKARIA AZIZI',
        'right' => 'PORTFOLIO / 2026',
    ],

    // Nama klien/brand yang pernah kamu kerjakan (tampil berjalan di bawah hero).
    // CONTOH — ganti dengan klien aslimu.
    'clients' => ['Kopi Lereng Lawu', 'Teh Tawangmangu', 'Keripik Mbok Sri', 'Batik Laweyan', 'Madu Karangpandan', 'Roti Kalisoro', 'Jamu Sekar', 'Lumen Labs'],

    // ------------------------------------------------------------
    // 4. PROYEK UTAMA (studi kasus besar di section "Work")
    //
    //  GAMBAR: buat folder assets/img/work/<slug>/ lalu taruh gambar di sana.
    //    - Gambar diurutkan berdasarkan nama file (01.jpg, 02.jpg, 03.jpg ...)
    //    - Gambar pertama = gambar besar, gambar ke-2 & ke-3 = gambar kecil
    //    - Format: jpg, jpeg, png, webp, avif, gif, svg
    //  Selama folder masih kosong, tampil mockup bawaan ('mock'):
    //    'pack'  = kemasan,  'brand' = logo/identitas,  'lumen' / 'orbit' / 'vela' / 'prism' = UI/UX
    //
    //  Semua proyek di bawah ini CONTOH — ganti dengan proyekmu.
    //  Mau tambah proyek? Salin satu blok [ ... ], tempel di bawahnya, ubah isinya.
    // ------------------------------------------------------------
    'projects' => [
        [
            'slug'    => 'kopi-lereng-lawu',
            'client'  => 'Kopi Lereng Lawu',
            'title'   => 'Lereng Lawu Coffee',
            'category'=> 'Packaging',
            'summary' => 'Packaging system for a single-origin coffee from the slopes of Mount Lawu: a stand-up pouch, gift box and label family that tells the farm story at a glance.',
            'role'    => 'Packaging · Illustration',
            'year'    => '2026',
            'tags'    => ['Packaging', 'Illustration', 'Print'],
            'metrics' => [['3×', 'shelf sell-through'], ['5', 'SKU variants']],
            'href'    => '',
            'mock'    => 'pack',
            'pack'    => ['brand' => 'Lereng Lawu', 'product' => 'Arabica Coffee', 'size' => '200 g', 'c1' => '#2a12c4', 'c2' => '#d6d1ff', 'c3' => '#f4ede2'],
        ],
        [
            'slug'    => 'teh-tawangmangu',
            'client'  => 'Teh Tawangmangu',
            'title'   => 'Tawangmangu Tea House',
            'category'=> 'Logo & Brand identity',
            'summary' => 'Logo, colour system and stationery for a hillside tea house. A leaf mark that also reads as a mountain path, built to work from a cup sleeve to a signboard.',
            'role'    => 'Logo · Brand identity',
            'year'    => '2025',
            'tags'    => ['Logo', 'Branding', 'Stationery'],
            'metrics' => [['12', 'brand assets'], ['1', 'week to launch']],
            'href'    => '',
            'mock'    => 'brand',
            'brand'   => ['name' => 'Tawangmangu', 'tagline' => 'Tea House · Est. 2025', 'c1' => '#160a6e', 'c2' => '#8a7bff', 'c3' => '#f4f3fb', 'c4' => '#ff8fb7'],
        ],
        [
            'slug'    => 'keripik-mbok-sri',
            'client'  => 'Keripik Mbok Sri',
            'title'   => 'Mbok Sri Chips',
            'category'=> 'Packaging',
            'summary' => 'A rebrand for a family snack business. Bold flavour colours, a friendlier wordmark and packaging that finally looks as good as it tastes.',
            'role'    => 'Packaging · Logo',
            'year'    => '2025',
            'tags'    => ['Packaging', 'Logo', 'UMKM'],
            'metrics' => [['+60%', 'reseller orders'], ['4', 'flavour packs']],
            'href'    => '',
            'mock'    => 'pack',
            'pack'    => ['brand' => 'Mbok Sri', 'product' => 'Cassava Chips', 'size' => '150 g', 'c1' => '#ff5a3c', 'c2' => '#ffd25a', 'c3' => '#fff6e6'],
        ],
        [
            'slug'    => 'lumen-app',
            'client'  => 'Lumen Labs',
            'title'   => 'Lumen App',
            'category'=> 'UI/UX',
            'summary' => 'Companion app concept for AI glasses: live translation and scene memory, designed voice-first so it stays out of the way.',
            'role'    => 'UI/UX Design',
            'year'    => '2026',
            'tags'    => ['UI/UX', 'Mobile', 'Concept'],
            'metrics' => [['24', 'screens'], ['1', 'clickable prototype']],
            'href'    => '',
            'mock'    => 'lumen',
        ],
    ],

    // ------------------------------------------------------------
    // 5. ARSIP / GALERI (section "Archive")
    //    Tidak perlu ditulis di sini: cukup taruh gambar di folder
    //    assets/img/gallery/  → otomatis tampil, urut nama file.
    //    Tips nama file = keterangan, mis. "01-logo-kopi-senja.png"
    //    akan tampil dengan label "Logo Kopi Senja".
    // ------------------------------------------------------------
    'archive' => [
        'title' => 'More from the archive',
        'intro' => 'Logos, labels, posters and social content: smaller pieces that did big jobs.',
    ],

    // ------------------------------------------------------------
    // 6. ABOUT
    // ------------------------------------------------------------
    'about' => [
        'statement' => 'I design packaging, logos and brand identities that help small and growing brands look as good as what they make, plus the screens their customers tap every day.',
        'body'      => 'Based in Karanganyar, Central Java, I work with local businesses and teams across Indonesia. From the first sketch to print-ready files, I make sure every detail survives the trip from screen to shelf.',
        // Angka statistik — GANTI dengan angka aslimu
        'stats'     => [
            ['value' => 4,  'suffix' => '+', 'label' => 'Years of design practice'],
            ['value' => 50, 'suffix' => '+', 'label' => 'Brands & products designed'],
            ['value' => 30, 'suffix' => '+', 'label' => 'Packaging projects'],
            ['value' => 20, 'suffix' => '+', 'label' => 'Happy clients'],
        ],
        'tools' => ['Adobe Illustrator', 'Photoshop', 'InDesign', 'Figma', 'Dimension', 'Blender', 'Procreate'],
    ],

    // ------------------------------------------------------------
    // 7. LAYANAN
    // ------------------------------------------------------------
    'services' => [
        ['title' => 'Logo & brand identity', 'desc' => 'Logos, colour palettes, typography and brand guidelines that make a business instantly recognisable.', 'chips' => ['Logo', 'Brand guide', 'Stationery']],
        ['title' => 'Packaging design',      'desc' => 'Pouches, boxes, labels and bottles designed for the shelf, with print-ready dielines and mockups.', 'chips' => ['Label', 'Box & pouch', 'Dieline']],
        ['title' => 'Print & promotion',     'desc' => 'Posters, brochures, menus, banners and catalogues that sell, from small runs to large format.', 'chips' => ['Poster', 'Brochure', 'Banner']],
        ['title' => 'Social media design',   'desc' => 'Feed templates and campaign visuals that keep a brand consistent every single post.', 'chips' => ['Instagram', 'Templates', 'Campaign']],
        ['title' => 'UI/UX design',          'desc' => 'Clean, usable interfaces for websites and mobile apps, from wireframe to a clickable prototype.', 'chips' => ['Website', 'Mobile app', 'Prototype']],
    ],

    'process' => [
        ['title' => 'Brief',   'time' => 'Day 1–2',  'desc' => 'We talk about your product, your customers and your competitors, and agree on what success looks like.'],
        ['title' => 'Sketch',  'time' => 'Day 3–5',  'desc' => 'Moodboards and rough concepts. Two or three directions so you can see real options, not guesses.'],
        ['title' => 'Design',  'time' => 'Week 2',   'desc' => 'The chosen direction gets refined into final artwork, with realistic mockups and up to two revision rounds.'],
        ['title' => 'Deliver', 'time' => 'Week 3',   'desc' => 'Print-ready files, dielines and source files, plus help talking to your printer so it comes out right.'],
    ],

    // ------------------------------------------------------------
    // PENGALAMAN KERJA (dipakai di versi /v2/) — CONTOH, ganti dengan riwayatmu
    // ------------------------------------------------------------
    'experience' => [
        ['place' => 'Freelance',            'where' => 'Karanganyar, Central Java', 'period' => '2022 — Now',  'role' => 'Graphic Designer',          'desc' => 'Packaging, logos and brand identities for UMKM and growing brands across Indonesia.'],
        ['place' => 'Creative Studio',      'where' => 'Surakarta',                 'period' => '2021 — 2022', 'role' => 'Junior Graphic Designer',   'desc' => 'Social media campaigns, print collateral and product labels for local clients.'],
        ['place' => 'Self-initiated',       'where' => 'Remote',                    'period' => '2020 — 2021', 'role' => 'UI/UX Designer (learning)', 'desc' => 'Designed app and website concepts to learn interaction design, from wireframe to prototype.'],
    ],

    // ------------------------------------------------------------
    // KHUSUS VERSI /v2/
    // ------------------------------------------------------------
    'v2' => [
        'hello'      => 'Hello!',
        'quote'      => 'Dzakaria’s packaging made our product the first one customers pick up from the shelf. Highly recommended!',
        'hire_title' => 'Why You Hire Me for Your Next Projects?',
        'hire_text'  => 'I combine a sharp eye for detail with an understanding of how products are actually sold: on a crowded shelf, in a marketplace thumbnail, or on a phone screen. You get designs that look good and work hard, delivered as clean, print-ready files.',
        'cta_title'  => 'Have an awesome project idea?',
        'cta_accent' => 'Let’s discuss',
        'ribbon'     => ['Logo', 'Packaging', 'Branding', 'Label', 'Poster', 'Social Media', 'UI/UX', 'Print'],
    ],

    // ------------------------------------------------------------
    // KHUSUS VERSI /v3/ — CONTOH, ganti dengan datamu (atau edit di dashboard)
    // ------------------------------------------------------------
    'v3' => [
        'hello'   => 'Hello There!',
        'tagline' => 'Graphic Designer based in Karanganyar, Indonesia',
        'quote'   => 'Dzakaria’s packaging made our product the first one customers pick up from the shelf.',
        'rating'  => '4.9',
        'hours'   => [['Monday – Friday', '09:00 – 17:00'], ['Saturday', '09:00 – 13:00']],
        'pricing_note' => 'Starting prices. Every project gets a custom quote after a short brief.',
    ],

    'education' => [
        ['place' => 'Universitas Sebelas Maret', 'title' => 'Visual Communication Design', 'period' => '2018 — 2022'],
        ['place' => 'Dicoding & Coursera',       'title' => 'UI/UX Design Certification',  'period' => '2021'],
        ['place' => 'SMK Negeri Karanganyar',    'title' => 'Multimedia',                  'period' => '2015 — 2018'],
    ],

    // Paket harga. 'featured' => '1' untuk paket yang disorot (warna oranye).
    'pricing' => [
        ['name' => 'Starter',  'price' => 'Rp 750K', 'unit' => '/ project', 'desc' => 'For new UMKM that need a clean, simple logo.', 'featured' => '',
         'features' => ['1 logo concept', '2 revision rounds', 'PNG, JPG & PDF files', 'Basic colour palette']],
        ['name' => 'Brand Pack', 'price' => 'Rp 2.5M', 'unit' => '/ project', 'desc' => 'Logo plus packaging for one product, ready to print.', 'featured' => '1',
         'features' => ['3 logo concepts', 'Label or pouch design for 1 SKU', 'Print-ready dieline', 'Realistic mockups', '3 revision rounds']],
        ['name' => 'Full Identity', 'price' => 'Rp 6M', 'unit' => '/ project', 'desc' => 'Complete brand identity for a growing business.', 'featured' => '',
         'features' => ['Logo system & brand guide', 'Packaging for up to 3 SKUs', 'Stationery & social templates', 'Source files included', 'Unlimited small revisions']],
    ],

    'faq' => [
        ['q' => 'How long does a project take?', 'a' => 'A logo usually takes 1–2 weeks, packaging 2–3 weeks. Bigger identity projects are scheduled after the brief.'],
        ['q' => 'Do you work with clients outside Karanganyar?', 'a' => 'Yes. Most projects run fully online over WhatsApp and video calls, anywhere in Indonesia.'],
        ['q' => 'What files will I receive?', 'a' => 'Print-ready PDF and AI files, PNG/JPG for digital use, and dielines for packaging. Source files are included in the bigger packages.'],
        ['q' => 'How does payment work?', 'a' => '50% down payment to start, 50% before the final files are sent. Bank transfer and e-wallets are accepted.'],
        ['q' => 'Can you help with printing?', 'a' => 'Yes. I prepare the files to your printer’s specs and can recommend printers around Solo Raya.'],
    ],

    // Testimoni — CONTOH, ganti dengan testimoni asli klienmu (atau hapus yang tidak perlu)
    'testimonials' => [
        ['quote' => 'Our new coffee pouch sold out at the first market we brought it to. People picked it up just because of the design.', 'name' => 'Pak Wahyu', 'role' => 'Owner, Kopi Lereng Lawu'],
        ['quote' => 'Fast, patient with revisions and the files were ready for the printer straight away.', 'name' => 'Bu Sri', 'role' => 'Owner, Keripik Mbok Sri'],
        ['quote' => 'The logo feels like us. Guests now take photos of our cups and signboard.', 'name' => 'Rina Kartika', 'role' => 'Manager, Teh Tawangmangu'],
        ['quote' => 'He understood the brief better than we did, and explained every design decision clearly.', 'name' => 'Andi Pratama', 'role' => 'Marketing, Roti Kalisoro'],
        ['quote' => 'Clean app screens, easy handoff to our developer. Would work with him again.', 'name' => 'Dimas Prakoso', 'role' => 'Founder, Lumen Labs'],
        ['quote' => 'Our Instagram finally looks like a real brand. Engagement went up within a month.', 'name' => 'Maya Lestari', 'role' => 'Owner, Jamu Sekar'],
    ],

    // Media sosial — isi link aslimu (biarkan '#' kalau belum ada)
    'social' => [
        ['label' => 'Instagram', 'href' => '#'],
        ['label' => 'Behance',   'href' => '#'],
        ['label' => 'Dribbble',  'href' => '#'],
        ['label' => 'LinkedIn',  'href' => '#'],
    ],
];
