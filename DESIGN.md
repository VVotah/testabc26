---
name: FoodLoop
description: Redistribusi makanan berlebih yang tercatat, tampil seperti label rak yang hangat.
colors:
  latar: "#f8f2e6"
  permukaan: "#fffdf9"
  permukaan-redup: "#f3ecde"
  garis: "#e6dcc8"
  garis-kuat: "#d3c7ae"
  teks: "#1f2b22"
  teks-redup: "#5c685e"
  hijau: "#1e5c2e"
  hijau-tekan: "#174a25"
  hijau-lembut: "#e4f0df"
  hijau-daun: "#5ba03c"
  oranye: "#f28a1e"
  oranye-tekan: "#de7a10"
  oranye-lembut: "#fdebd3"
  oranye-teks: "#8a4200"
  teks-di-oranye: "#3a1d00"
  kuning: "#ffd84a"
  kuning-teks: "#4a3600"
  merah: "#c2351f"
  merah-tekan: "#a32a17"
  merah-lembut: "#fbe3dd"
  merah-teks: "#9b2414"
  netral-lembut: "#eee7d8"
  netral-teks: "#505a51"
  putih: "#ffffff"
typography:
  display:
    fontFamily: "Sofia Sans Extra Condensed, Sofia Sans, Arial Narrow, sans-serif"
    fontSize: "clamp(2.75rem, 4.6vw, 4.5rem)"
    fontWeight: 800
    lineHeight: 0.95
    letterSpacing: "0.005em"
  headline:
    fontFamily: "Sofia Sans Extra Condensed, Sofia Sans, Arial Narrow, sans-serif"
    fontSize: "clamp(2.25rem, 1.7rem + 1.8vw, 3rem)"
    fontWeight: 800
    lineHeight: 1
    letterSpacing: "0.005em"
  numeral:
    fontFamily: "Sofia Sans Extra Condensed, Sofia Sans, Arial Narrow, sans-serif"
    fontSize: "3.25rem"
    fontWeight: 800
    lineHeight: 1
    fontFeature: "tnum"
  title:
    fontFamily: "Sofia Sans, system-ui, -apple-system, Segoe UI, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "-0.005em"
  body:
    fontFamily: "Sofia Sans, system-ui, -apple-system, Segoe UI, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 400
    lineHeight: 1.55
  caption:
    fontFamily: "Sofia Sans, system-ui, -apple-system, Segoe UI, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 400
    lineHeight: 1.4
  label:
    fontFamily: "Azeret Mono, ui-monospace, Cascadia Mono, Consolas, monospace"
    fontSize: "0.6875rem"
    fontWeight: 500
    lineHeight: 1.45
    letterSpacing: "0.05em"
  data:
    fontFamily: "Azeret Mono, ui-monospace, Cascadia Mono, Consolas, monospace"
    fontSize: "0.875rem"
    fontWeight: 500
    letterSpacing: "-0.01em"
    fontFeature: "tnum"
rounded:
  cap: "6px"
  sm: "8px"
  md: "10px"
  ikon: "12px"
  lg: "16px"
  pil: "999px"
spacing:
  xs: "0.5rem"
  sm: "0.75rem"
  md: "1rem"
  lg: "1.25rem"
  xl: "1.75rem"
components:
  button-primary:
    backgroundColor: "{colors.hijau}"
    textColor: "{colors.putih}"
    typography: "{typography.title}"
    rounded: "{rounded.md}"
    padding: "0.5rem 1.05rem"
  button-primary-hover:
    backgroundColor: "{colors.hijau-tekan}"
    textColor: "{colors.putih}"
  button-accent:
    backgroundColor: "{colors.oranye}"
    textColor: "{colors.teks-di-oranye}"
    rounded: "{rounded.md}"
    padding: "0.5rem 1.05rem"
  button-accent-hover:
    backgroundColor: "{colors.oranye-tekan}"
    textColor: "{colors.teks-di-oranye}"
  button-secondary:
    backgroundColor: "{colors.permukaan}"
    textColor: "{colors.teks}"
    rounded: "{rounded.md}"
    padding: "0.5rem 1.05rem"
  button-secondary-hover:
    backgroundColor: "{colors.hijau-lembut}"
    textColor: "{colors.hijau-tekan}"
  button-danger:
    backgroundColor: "{colors.merah}"
    textColor: "{colors.putih}"
    rounded: "{rounded.md}"
    padding: "0.5rem 1.05rem"
  button-danger-hover:
    backgroundColor: "{colors.merah-tekan}"
    textColor: "{colors.putih}"
  button-danger-outline:
    backgroundColor: "{colors.permukaan}"
    textColor: "{colors.merah-teks}"
    rounded: "{rounded.md}"
    padding: "0.5rem 1.05rem"
  button-small:
    rounded: "{rounded.sm}"
    padding: "0.3rem 0.7rem"
  panel:
    backgroundColor: "{colors.permukaan}"
    textColor: "{colors.teks}"
    rounded: "{rounded.lg}"
    padding: "1.25rem"
  stat-card:
    backgroundColor: "{colors.permukaan}"
    textColor: "{colors.teks}"
    typography: "{typography.numeral}"
    rounded: "{rounded.lg}"
    padding: "1.125rem 1.25rem 1.2rem"
  stat-icon-tile:
    backgroundColor: "{colors.hijau-lembut}"
    textColor: "{colors.hijau}"
    rounded: "{rounded.ikon}"
    size: "44px"
  stat-icon-tile-alt:
    backgroundColor: "{colors.oranye-lembut}"
    textColor: "{colors.oranye-teks}"
    rounded: "{rounded.ikon}"
    size: "44px"
  input:
    backgroundColor: "{colors.permukaan}"
    textColor: "{colors.teks}"
    typography: "{typography.body}"
    rounded: "{rounded.md}"
    padding: "0.575rem 0.8rem"
  table-head:
    backgroundColor: "{colors.hijau}"
    textColor: "{colors.putih}"
    typography: "{typography.label}"
    padding: "0.75rem 1rem"
  nav-item:
    backgroundColor: "transparent"
    textColor: "{colors.teks}"
    rounded: "{rounded.sm}"
    padding: "0.4rem 0.75rem"
    height: "40px"
  nav-item-hover:
    backgroundColor: "{colors.hijau-lembut}"
    textColor: "{colors.hijau-tekan}"
  nav-item-active:
    backgroundColor: "{colors.hijau}"
    textColor: "{colors.putih}"
  status-positif:
    backgroundColor: "{colors.hijau-lembut}"
    textColor: "{colors.hijau}"
    typography: "{typography.label}"
    rounded: "{rounded.cap}"
    padding: "0.22rem 0.5rem"
  status-tunggu:
    backgroundColor: "{colors.kuning}"
    textColor: "{colors.kuning-teks}"
    typography: "{typography.label}"
    rounded: "{rounded.cap}"
    padding: "0.22rem 0.5rem"
  status-proses:
    backgroundColor: "{colors.oranye-lembut}"
    textColor: "{colors.oranye-teks}"
    typography: "{typography.label}"
    rounded: "{rounded.cap}"
    padding: "0.22rem 0.5rem"
  status-negatif:
    backgroundColor: "{colors.merah-lembut}"
    textColor: "{colors.merah-teks}"
    typography: "{typography.label}"
    rounded: "{rounded.cap}"
    padding: "0.22rem 0.5rem"
  status-netral:
    backgroundColor: "{colors.netral-lembut}"
    textColor: "{colors.netral-teks}"
    typography: "{typography.label}"
    rounded: "{rounded.cap}"
    padding: "0.22rem 0.5rem"
  label-ribbon:
    backgroundColor: "{colors.hijau}"
    textColor: "{colors.putih}"
    typography: "{typography.label}"
    padding: "0.55rem 1rem"
  sticker:
    backgroundColor: "{colors.kuning}"
    textColor: "{colors.kuning-teks}"
    rounded: "{rounded.cap}"
    padding: "0.3rem 0.75rem 0.28rem"
---

# Design System: FoodLoop

Satu sumber gaya: `public/css/foodloop.css` (semua token di `:root`, awalan `--fl-`). Nama token di frontmatter memakai nama variabel CSS tanpa awalan, jadi `colors.hijau` = `--fl-hijau`, `rounded.lg` = `--fl-sudut-besar`, dan seterusnya. Contoh hidup semua komponen: halaman `/komponen` (lokal).

## Overview

**Creative North Star: "Label Kedaluwarsa, versi hangat"**

FoodLoop memperlakukan setiap makanan seperti barang di rak yang diberi label: tenggat, sisa porsi, dan kode dicetak tegas dengan huruf sempit tebal dan huruf mono kapital, seperti label harga dan label kedaluwarsa. Di sekeliling label itu antarmukanya ramah: latar krem hangat, kartu gading bergaris tipis lembut, sudut agak membulat (10 sampai 16px), dan bayangan cokelat yang nyaris tak terlihat. Data dicetak, ruangnya menyambut.

Kepadatannya sedang: dashboard kerja untuk Mitra dan Admin di laptop, dan layar aksi cepat untuk Penerima dan Relawan di HP. Hijau gelap dari logo memegang semua yang menunjukkan "di sini" dan "lakukan ini"; oranye logo dijatah sangat hemat. Hiasan hanya boleh berasal dari dunia label: pita, garis sobek putus-putus, garis pindai, stiker miring.

Versi pertama (latar abu-abu, garis hitam tebal, sudut siku) ditolak pemilik produk karena terlalu keras dan dingin. Versi hangat ini yang disetujui (11 Nov 2026).

**Key Characteristics:**
- Latar krem hangat, kartu gading, garis tipis 1px warna pasir.
- Hijau gelap logo sebagai satu-satunya warna utama; oranye hanya untuk aksen yang dijatah.
- Judul huruf sempit tebal (Sofia Sans Extra Condensed 800); isi Sofia Sans; data dan label kecil Azeret Mono kapital.
- Status selalu berupa cap kecil berwarna makna, dari satu pemetaan baku.
- Sudut agak membulat, bayangan cokelat sangat halus, gerak singkat.

## Colors

Netral hangat berbasis krem dan pasir, satu warna utama hijau gelap, satu aksen oranye, dan tiga warna makna (kuning, merah, abu hangat).

### Primary
- **Hijau Hutan Logo** (`--fl-hijau`): menu sidebar aktif, tombol utama, kepala tabel, pita label, avatar, kotak centang terpilih, halaman paginasi aktif, garis fokus. Teks putih di atasnya.
- **Hijau Hutan Tekan** (`--fl-hijau-tekan`): hover dan tekan tombol utama, warna judul besar halaman masuk, angka tenggat di label contoh, warna teks hover menu dan tombol sekunder.
- **Hijau Embun** (`--fl-hijau-lembut`): latar hover menu dan tombol sekunder, cap status positif, ubin ikon statistik ganjil, ubin ikon tampilan kosong, cap jam di topbar, pesan sukses.
- **Hijau Daun** (`--fl-hijau-daun`): hijau muda logo, cadangan untuk hiasan kecil. Belum dipakai di komponen mana pun; jangan dipakai untuk teks.

### Secondary
- **Oranye Logo** (`--fl-oranye`): tombol aksen (`btn-aksen`) saja, dengan teks `--fl-teks-di-oranye`. Hover `--fl-oranye-tekan`, yang juga mewarnai kata penutup judul halaman masuk.
- **Oranye Krim** (`--fl-oranye-lembut`) dengan **Oranye Bata** (`--fl-oranye-teks`): ubin ikon statistik genap, cap status "Dalam pengantaran", pesan info.

### Tertiary (warna makna)
- **Kuning Stiker** (`--fl-kuning`) dengan teks `--fl-kuning-teks`: hanya cap status yang menunggu tindakan dan stiker label ("Gratis").
- **Merah Bata** (`--fl-merah`, tekan `--fl-merah-tekan`): tombol bahaya, tepi kolom isian yang salah. Pasangannya `--fl-merah-lembut` dan `--fl-merah-teks` untuk cap status negatif, pesan galat, tombol Hapus bergaris, dan teks `invalid-feedback`.

### Neutral
- **Krem Rak** (`--fl-latar`): latar halaman dan topbar (90% buram dengan blur).
- **Gading** (`--fl-permukaan`): kartu, panel, tabel, sidebar, kolom isian, tombol sekunder, modal, dropdown.
- **Gading Redup** (`--fl-permukaan-redup`): isian nonaktif, hover baris tabel, latar `code`, gambar daftar yang belum dimuat.
- **Garis Pasir** (`--fl-garis`): garis 1px semua kartu, pemisah, tepi sidebar dan topbar.
- **Garis Pasir Tua** (`--fl-garis-kuat`): tepi kolom isian dan tombol sekunder, garis sobek putus-putus.
- **Tinta Hijau Arang** (`--fl-teks`): teks isi dan judul.
- **Tinta Redup** (`--fl-teks-redup`): keterangan, label mono, teks bantuan; lolos 4,5:1 di latar dan permukaan.
- **Abu Hangat** (`--fl-netral-lembut`) dengan `--fl-netral-teks`: cap status yang sudah lewat, tombol utama nonaktif, pesan sekunder.

### Named Rules
**The Satu Hijau Rule.** Hijau gelap adalah satu-satunya warna utama. Semua penanda "kamu di sini" dan "lakukan ini" memakai hijau gelap; tombol `btn-primary`, `btn-dark`, dan `btn-success` Bootstrap sengaja dipetakan ke hijau yang sama.

**The Oranye Dijatah Rule.** Oranye logo hanya muncul di tiga tempat: satu ajakan terpenting per halaman (`btn-aksen`), kata penutup judul halaman masuk, dan ubin ikon statistik selang-seling. Di luar itu oranye hanya hadir sebagai cap "proses" berwarna krim.

**The Kuning Berarti Menunggu Rule.** Kuning penuh hanya untuk cap status yang menunggu tindakan dan stiker label. Kuning bukan warna hiasan, bukan latar kartu, bukan tombol.

**The Status Baku Rule.** Warna status tidak dipilih per halaman. Komponen `<x-status :nilai="...">` (`resources/views/components/status.blade.php`) memetakan setiap status baku ke satu jenis cap:
- **positif** (hijau embun): Aktif, Terverifikasi, Disetujui, Selesai, Diserahkan, Terbit, Berlangsung, Ditindaklanjuti, Berhasil.
- **tunggu** (kuning stiker): Menunggu, Menunggu verifikasi, Menunggu Persetujuan, Dijadwalkan, Baru, Ditinjau, Akan Datang, Draf.
- **proses** (oranye krim): Dalam pengantaran.
- **negatif** (merah lembut): Ditolak, Ditangguhkan, Gagal.
- **netral** (abu hangat): Ditutup, Habis, Kedaluwarsa, Dibatalkan, Diarsipkan, dan status lain yang tidak dikenal.
Status baru ditambahkan ke pemetaan itu, bukan diberi warna di view.

## Typography

**Display Font:** Sofia Sans Extra Condensed (dengan Sofia Sans, Arial Narrow) — `--fl-huruf-judul`
**Body Font:** Sofia Sans (dengan system-ui, Segoe UI) — `--fl-huruf-isi`
**Label/Mono Font:** Azeret Mono (dengan ui-monospace, Cascadia Mono, Consolas) — `--fl-huruf-data`

**Character:** Huruf sempit tebal untuk judul dan angka besar terasa seperti cetakan label rak; Sofia Sans menjaga isi tetap ramah dan mudah dibaca; Azeret Mono kapital memberi rasa "tercatat" pada tanggal, jam, kode, dan nama kolom. Semua dimuat dari Google Fonts di `layouts/partials/kepala.blade.php`.

### Hierarchy
- **Display** (Extra Condensed 800, clamp 2.75 sampai 4.5rem, line-height 0.95): judul besar halaman masuk, hijau tekan dengan kata terakhir oranye. Tiap baris dibungkus sendiri supaya patahannya terjaga.
- **Headline** (Extra Condensed 800, clamp 2.25 sampai 3rem, line-height 1): judul halaman `h1` di `<x-kepala-halaman>`; juga `h2`, judul kartu masuk (3rem) dan judul modal (1.75rem).
- **Numeral** (Extra Condensed 800, 3.25rem, angka tabular): angka besar kartu statistik (2.75rem di HP). Angka label contoh memakai skala yang sama sampai 5.5rem.
- **Title** (Sofia Sans 700, 1.0625rem, line-height 1.3): judul panel, judul kartu, judul bagian form. `h3` sampai `h6` memakai Sofia Sans 700.
- **Caption** (Sofia Sans 400, 0.8125rem, line-height 1.4): teks bantuan form (`form-text`), pesan salah (`invalid-feedback`), keterangan di kartu pilihan, kotak unggah, daftar baris, dan catatan statistik.
- **Body** (Sofia Sans 400, 0.9375rem, line-height 1.55): teks isi, tombol (700), menu (600), label form (700, 0.875rem). Keterangan kepala halaman dibatasi 62ch.
- **Label** (Azeret Mono 500, 0.6875rem, letter-spacing 0.05em, KAPITAL): nama kolom tabel, label statistik, keterangan kecil (`fl-cap`), keterangan "Segera hadir", cap status (600, 0.04em).
- **Data** (Azeret Mono, angka tabular, letter-spacing -0.01em): jam, tanggal, kode, angka di sel tabel (`fl-data`, `td.text-end`).

### Named Rules
**The Mono Hanya Untuk Data Rule.** Huruf mono kapital hanya untuk label kecil, tanggal, jam, angka, kode, dan cap status. Kalimat, judul, tombol, dan menu tidak pernah mono.

**The Judul Sempit Rule.** Huruf sempit tebal hanya untuk `h1`/`h2`, judul modal, dan angka besar. Judul panel dan bagian form tetap Sofia Sans 700 supaya halaman punya satu suara judul besar saja.

## Layout

Kerangka dashboard: sidebar kiri tetap selebar `--fl-lebar-sidebar` (240px) berlatar gading, topbar lengket setinggi `--fl-tinggi-topbar` (64px) berlatar krem buram, lalu konten dengan padding 1.75rem dan lebar maksimum 1280px. Topbar memuat cap tanggal dan jam (mono, jam di cap hijau embun), tombol Notifikasi, dan tombol akun berbentuk pil.

Ritme jarak berbasis rem: 0.5rem antar tombol, 0.75rem sampai 1rem antar elemen kecil dan antar kartu statistik, 1.25rem padding panel, 1.5rem di bawah kepala halaman, 1.75rem padding konten dan jarak sebelum tombol akhir form. Kartu statistik memakai grid `auto-fit` minimal 200px; panel berpasangan memakai `row g-4` dengan `col-xl-6`.

Halaman masuk: dua kolom (1.3fr dan 0.75fr), kiri krem berisi logo, judul display, dan label contoh; kanan panel gading dengan form selebar maksimum 380px. Halaman daftar: kepala tipis bergaris bawah dan satu kartu form (maksimum 720px) di tengah.

Responsif: di bawah 1200px label contoh turun di bawah judul. Di bawah 992px sidebar menjadi laci geser (maksimum 300px) dengan tirai gelap, menu setinggi 46px, tombol kecil dan tombol topbar minimal 44px; halaman masuk berurutan logo, judul, kartu form, label contoh. Di bawah 576px kartu statistik dua kolom, nama akun dan tanggal disembunyikan, tombol akhir form dan aksi kepala halaman melebar penuh.

## Elevation & Depth

Hampir datar: kedalaman datang dari perbedaan krem dan gading ditambah garis tipis, dibantu bayangan cokelat hangat yang sangat halus. Tidak ada bayangan abu-abu atau hitam, dan tidak ada bayangan keras bergeser.

### Shadow Vocabulary
- **Diam** (`--fl-bayangan`: `0 1px 2px rgba(70,52,20,.05), 0 8px 20px -14px rgba(70,52,20,.22)`): panel, kartu, kartu statistik, bungkus tabel, kartu form masuk di HP.
- **Angkat** (`--fl-bayangan-angkat`: `0 2px 6px rgba(70,52,20,.08), 0 22px 44px -20px rgba(70,52,20,.35)`): modal, dropdown, sidebar laci yang terbuka, label contoh.
- **Fokus** (`--fl-fokus`: `0 0 0 3px rgba(30,92,46,.28)`): cincin fokus kolom isian dan tombol; versi merah untuk kolom yang salah. Elemen lain memakai garis 2px hijau dengan jarak 2px.

### Named Rules
**The Bayangan Cokelat Rule.** Semua bayangan berwarna cokelat hangat (70, 52, 20) dan lembut. Hanya dua tingkat: diam untuk permukaan, angkat untuk lapisan yang melayang.

## Shapes

Sudut agak membulat, bertingkat menurut ukuran: 6px untuk cap status, cap jam, dan stiker; 8px (`--fl-sudut-kecil`) untuk tombol kecil dan item menu; 10px (`--fl-sudut`) untuk tombol, kolom isian, pesan, dan tampilan kosong; 12px untuk ubin ikon; 16px (`--fl-sudut-besar`) untuk kartu, panel, tabel, modal, dropdown, dan label. Lingkaran penuh hanya untuk avatar dan tombol akun berbentuk pil.

Garis selalu 1px. Garis utuh memisahkan; garis putus-putus `--fl-garis-kuat` berarti garis sobek label (bawah kartu statistik, rincian dan kode di label contoh) atau tempat yang belum terisi (tampilan kosong di luar panel). Ikon adalah satu set ikon garis SVG (`<x-ikon>`, garis 1.75, ujung membulat, warna mengikuti teks).

## Components

### Buttons
Tegas dan ramah: huruf Sofia Sans 700, tanpa huruf kapital semua.
- **Shape:** sudut 10px; tombol kecil (`btn-sm`) 8px dan padding 0.3rem 0.7rem.
- **Utama** (`btn-utama`, juga `btn-primary`/`btn-dark`/`btn-success`): hijau gelap, teks putih, padding 0.5rem 1.05rem. Satu per layar.
- **Aksen** (`btn-aksen`, juga `btn-warning`): oranye logo dengan teks cokelat tua, untuk satu ajakan terpenting.
- **Sekunder** (`btn-sekunder`, juga `btn-outline-*`, `btn-light`): gading dengan tepi pasir tua; hover hijau embun dan teks hijau tekan. Untuk Batal, Ubah, Lihat semua.
- **Bahaya** (`btn-bahaya`/`btn-danger`): merah bata, teks putih. **Hapus** (`btn-outline-danger`): gading dengan teks dan tepi merah.
- **Hover / Focus / Active:** warna berganti 0.15s; ditekan turun 1px; fokus memakai cincin `--fl-fokus`. Saat form dikirim, kelas `memproses` meredupkan tombol dan menahannya. Tombol nonaktif tetap penuh warna netral, tidak dibuat transparan.

### Status Stamps (cap status)
- **Style:** cap kecil mono kapital 0.6875rem/600, sudut 6px, tanpa tepi, warna dari Status Baku Rule. Kelas Bootstrap `badge` ikut gaya ini.
- **Rule:** selalu lewat `<x-status>`.

### Cards / Containers
- **Corner Style:** 16px.
- **Background:** gading di atas latar krem.
- **Shadow Strategy:** bayangan diam.
- **Border:** 1px garis pasir.
- **Internal Padding:** 1.25rem (`fl-panel`); kepala panel berisi judul Title dan aksi di kanan, dipisah garis bawah 1px.

### Stat Card (kartu statistik)
Potongan label rak: label mono di kiri atas, ubin ikon 44px di kanan (selang-seling hijau embun dan oranye krim), angka besar huruf sempit, dan catatan opsional di bawah garis sobek putus-putus. Angka yang belum ada datanya tampil "Segera hadir" dalam mono kecil, tidak pernah angka karangan; angka 0 tetap 0.

### Inputs / Fields
- **Style:** gading, tepi 1px pasir tua, sudut 10px, padding 0.575rem 0.8rem, teks 500; label form tebal di atas kolom.
- **Focus:** tepi hijau gelap dan cincin `--fl-fokus`.
- **Error / Disabled:** tepi merah bata, cincin merah, pesan `invalid-feedback` merah tebal 0.8125rem; nonaktif berlatar gading redup.
- **Kolom bergabung** (`fl-gabung`): kolom dan tombol menempel dalam satu bingkai, dipisah satu garis tipis (mis. kata sandi + Lihat).

### Tables
- Dibungkus `fl-tabel` (gading, sudut 16px, bayangan diam, bisa digeser di HP). Kepala tabel hijau gelap dengan label mono putih; sel padding 0.75rem 1rem; angka rata kanan memakai mono tabular.

### Navigation
- **Sidebar:** logo di atas, item menu Sofia Sans 600, tinggi 40px, sudut 8px. Hover hijau embun; aktif hijau gelap teks putih dengan `aria-current`. Menu modul yang belum tersedia dikumpulkan di bawah keterangan mono "Segera hadir", berwarna redup, tanpa garis putus-putus.
- **Mobile:** laci geser dengan tirai, dibuka tombol Menu di topbar.

### Empty State (tampilan kosong)
Ubin ikon 44px hijau embun, judul tebal, kalimat arahan maksimum 44ch, dan tombol opsional. Di luar panel dibingkai garis putus-putus; di dalam panel tanpa bingkai kedua.

### Label Contoh (signature)
Gambaran listing sebagai label rak di halaman masuk: kertas gading bersudut 16px dengan bayangan angkat, pita hijau berisi label mono, nama makanan huruf sempit, "Layak sampai" dengan jam sangat besar, rincian dua kolom dipisah garis sobek, garis pindai seperti kode batang, kode mono, dan stiker kuning "Gratis" miring 7 derajat. Gerak khas satu kali: label keluar dari atas seperti kertas pencetak (0.9s), lalu stiker ditempel (0.3s); dimatikan bila pengguna memilih gerak dikurangi.

## Do's and Don'ts

### Do:
- **Do** pakai token `--fl-*` dan kelas yang sudah ada (Bootstrap bertema atau `fl-*`); butuh gaya baru, tambahkan di `public/css/foodloop.css`.
- **Do** pakai hijau gelap (`--fl-hijau`) untuk menu aktif, tombol utama, kepala tabel, dan pita label.
- **Do** tampilkan status hanya lewat `<x-status>`, dan tambah status baru ke pemetaannya.
- **Do** cetak tanggal, jam, angka, dan kode dengan Azeret Mono angka tabular.
- **Do** tampilkan angka yang belum ada sebagai "Segera hadir" atau kosong, bukan angka contoh.
- **Do** jaga sasaran sentuh minimal 44px di HP dan kontras teks minimal 4,5:1.

### Don't:
- **Don't** kembali ke versi yang ditolak pemilik produk: latar abu-abu, garis hitam tebal, sudut siku, kesan keras dan dingin.
- **Don't** pakai oranye di luar tombol aksen, kata penutup judul halaman masuk, ubin ikon selang-seling, dan cap proses.
- **Don't** pakai kuning untuk apa pun selain cap status menunggu dan stiker label.
- **Don't** pakai huruf mono kapital untuk kalimat, judul, tombol, atau menu.
- **Don't** pasang label mono kecil sebagai hiasan di atas judul halaman atau judul panel; label mono hanya menamai sebuah nilai.
- **Don't** tulis warna, bayangan, ukuran huruf, atau `style="..."` berwarna di view, dan jangan menambah font, CDN, atau file CSS lain.
- **Don't** pakai bayangan abu-abu, hitam, atau keras bergeser; hanya dua bayangan cokelat yang tercatat.
- **Don't** pakai ikon emoji atau ikon dari paket lain; ikon hanya dari `<x-ikon>`.
