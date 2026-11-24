# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Empat peran, semuanya pengguna Indonesia, berbahasa Indonesia:

- **Mitra/Donatur**: pengelola kantin, restoran, toko roti, katering, atau panitia acara yang punya makanan berlebih. Mengelola listing, jadwal surplus, permintaan masuk, dan serah terima, umumnya di laptop di sela jam operasional.
- **Penerima**: individu, komunitas, atau lembaga yang membutuhkan makanan. Mencari listing, mengajukan permintaan, dan menjadwalkan pickup, umumnya dari HP.
- **Relawan**: mengambil tugas antar dan memperbarui status pengantaran di lapangan, dari HP.
- **Admin**: memverifikasi mitra, menindaklanjuti laporan, mengelola artikel dan kampanye, di laptop.

HP dan laptop sama pentingnya (dikonfirmasi pemilik produk): Penerima dan Relawan di HP, Mitra dan Admin di laptop. Produk ini juga didemokan ke dosen pada Sprint Review.

## Product Purpose

FoodLoop adalah aplikasi web redistribusi dan sharing makanan berlebih yang masih layak konsumsi (SDG 12, target 12.3 dan 12.5). Aplikasi ini menghubungkan pemilik makanan berlebih dengan pihak yang membutuhkan sebelum makanan itu kedaluwarsa, dan mencatat setiap penyaluran sampai serah terima. Berhasil berarti makanan tersalurkan tepat waktu dan jumlah porsi yang terselamatkan tercatat serta terlihat.

## Positioning

Penyaluran yang tercatat dari ujung ke ujung: mitra terverifikasi, stok porsi berkurang otomatis saat permintaan disetujui, pickup dijadwalkan di titik pengambilan, dan serah terima dikonfirmasi dengan kode pickup. Bukan sekadar papan pengumuman makanan gratis.

## Operating Context

- Waktu adalah inti: setiap listing punya batas kedaluwarsa dan sisa porsi, dan sistem menyembunyikan yang habis atau kedaluwarsa.
- Alur utama: Mitra daftar, Admin verifikasi, Mitra buat titik pengambilan dan listing, Penerima cari dan ajukan permintaan, Donatur setujui, jadwal pickup, Relawan antar bila perlu, serah terima dengan kode, ulasan, notifikasi, laporan dampak.
- Di luar alur itu: forum sharing antarindividu, artikel edukasi, kampanye, dan moderasi laporan.
- Dikerjakan tujuh mahasiswa, satu modul per orang, dengan metode Scrum dua sprint.

## Capabilities and Constraints

- 21 PBI dalam tujuh modul, ditambah registrasi, login, dashboard, dan verifikasi mitra. Tidak menambah fitur di luar proposal.
- Stack tetap: Laravel + MySQL, Blade, Bootstrap 5 via CDN, JavaScript vanilla. Tanpa Tailwind, React, Vue, atau package baru tanpa persetujuan.
- Struktur halaman, menu, dan isi mengikuti mockup Figma (wireframe); gaya visual bebas ditentukan.
- Semua gaya di satu file `public/css/foodloop.css` dan komponen Blade bersama, supaya enam anggota lain tinggal memakai.
- Istilah dan status baku memakai bahasa Indonesia persis seperti di `docs/KONTEKS_PROYEK.md`.
- Tidak ada pembayaran online, tidak ada pelacakan lokasi real-time, notifikasi hanya di dalam aplikasi.

## Brand Commitments

- Nama: FoodLoop.
- Logo: `public/img/logo.png` dan `public/img/ikon.png` dipakai apa adanya (panah melingkar hijau dan oranye, kotak donasi berisi roti, sayur, dan apel).
- Hanya logo yang mengikat. Palet antarmuka bebas ditentukan arah desain (dikonfirmasi pemilik produk).
- Harus terasa premium, bukan seperti template atau buatan AI. Yang dinilai murahan oleh pemilik produk pada versi pertama: huruf membulat, warna pucat, kartu generik tanpa karakter, ketiadaan gambar makanan, dan detail yang kasar.
- Juga tidak boleh keras dan dingin: versi abu dengan garis hitam tebal dan sudut siku ditolak pemilik produk (11 Nov 2026). Yang disetujui: latar krem hangat, hijau gelap sebagai warna utama, garis tipis lembut, sudut agak membulat, kesan ramah, nyaman, dan hangat. Rinciannya di DESIGN.md.

## Evidence on Hand

- Proposal: `docs/Proposal PPL_V01_Kelompok A-A Baru.pdf`. Mockup wireframe: `docs/mockup/`.
- Logo asli: `docs/logo/`.
- Belum ada foto makanan milik sendiri. Foto stok berlisensi bebas boleh dipakai setelah sumbernya ditunjukkan dan disetujui.
- Belum ada data nyata, testimoni, atau angka dampak. Jangan mengarang angka; angka yang belum tersedia ditampilkan sebagai belum ada.

## Product Principles

1. Waktu dan sisa porsi selalu terlihat lebih dulu daripada hiasan.
2. Setiap layar menjawab "apa langkah berikutnya" untuk peran yang sedang login.
3. Kepercayaan dibangun dari status yang jelas dan jujur, bukan dari klaim.
4. Satu sistem tampilan untuk tujuh modul: anggota tim merakit, tidak mendesain ulang.

## Accessibility & Inclusion

Kontras teks minimal 4,5:1, seluruh alur bisa dijalankan dengan keyboard, sasaran sentuh minimal 44px di HP, dan tata letak responsif (NFR-07 proposal).
