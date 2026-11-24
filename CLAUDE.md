# CLAUDE.md — FoodLoop

File ini dibaca otomatis oleh Claude Code. Kalau kamu Claude biasa (chat) dan file ini di-upload pengguna, anggap ini aturan proyek yang wajib diikuti. Baca sampai habis sebelum nulis kode.

## Proyek
FoodLoop = web redistribusi surplus makanan (Tubes PPL, Telkom University, kelompok A-A, SI4808).
Baca dulu, urut: `docs/KONTEKS_PROYEK.md` (tim, 21 PBI, tabel, istilah dan status baku) lalu `PANDUAN_TIM.md` (aturan Git).
Sumber kebenaran: **proposal** (`docs/Proposal PPL_V01_Kelompok A-A Baru.pdf`, ERD kolom + tipe data di Bab 4.3) dan **mockup Figma** (`docs/mockup/mockup_README.md`).
Kalau ragu, ikuti proposal. Jangan mengarang fitur, tabel, atau kolom di luar proposal. Kriteria selesai tiap PBI ada di kartu Jira (FOOD-xx).

## Stack (jangan diganti)
- Laravel (PHP) + MySQL, arsitektur MVC monolith
- Tampilan: **Blade + Bootstrap 5 via CDN**, ditambah font Sofia Sans, Sofia Sans Extra Condensed, dan Azeret Mono via Google Fonts (sudah dipasang di layout). Tidak pakai Tailwind, tidak pakai React/Vue.
- JavaScript seperlunya (vanilla). Jangan tambah package Composer/NPM tanpa tanya dulu ke Nug.
- Package yang sudah disetujui kalau dibutuhkan: Leaflet + OpenStreetMap (peta, PBI-09), barryvdh/laravel-dompdf (PDF, PBI-18).
- Foto/berkas: storage lokal Laravel (`php artisan storage:link`).

## Penamaan (ikut ERD proposal Bab 4.3)
- Tabel dan kolom: bahasa Indonesia, snake_case, persis seperti ERD. Contoh: `pengguna`, `id_pengguna`, `kata_sandi`, `status_akun`.
- Tabel `pengguna` (bukan `users`). Primary key `id_pengguna`. Kolom password = `kata_sandi`.
- Teks di layar: bahasa Indonesia.

## Struktur modul (SATU anggota = SATU modul)
| Modul | Pemilik | PBI |
|---|---|---|
| 1 Akun & Mitra | Nug | 01, 02, 03 |
| 2 Listing Donasi | Ica | 04, 05, 06 |
| 3 Pencarian & Permintaan | Nico | 07, 08, 09 |
| 4 Pickup & Logistik | Bitha | 10, 11, 12 |
| 5 Forum Sharing | Intan | 13, 14, 15 |
| 6 Edukasi & Dampak | Rhein | 16, 17, 18 |
| 7 Moderasi & Kualitas | Clara | 19, 20, 21 |

Tiap modul punya file sendiri, jadi tidak tabrakan:
- Route: `routes/modul/<nama_modul>.php`
- Controller: `app/Http/Controllers/<Modul>/...`
- View: `resources/views/<modul>/...`
- Seeder: `database/seeders/<Modul>Seeder.php`

Nama file route dan seeder tiap modul (sudah disiapkan, tinggal diisi; jangan bikin nama lain):
| Modul | Route | Seeder |
|---|---|---|
| 1 | `routes/modul/akun_mitra.php` | `AkunMitraSeeder` |
| 2 | `routes/modul/listing_donasi.php` | `ListingDonasiSeeder` |
| 3 | `routes/modul/pencarian_permintaan.php` | `PencarianPermintaanSeeder` |
| 4 | `routes/modul/pickup_logistik.php` | `PickupLogistikSeeder` |
| 5 | `routes/modul/forum_sharing.php` | `ForumSharingSeeder` |
| 6 | `routes/modul/edukasi_dampak.php` | `EdukasiDampakSeeder` |
| 7 | `routes/modul/moderasi_kualitas.php` | `ModerasiKualitasSeeder` |

Semua file di `routes/modul/` dimuat otomatis oleh `routes/web.php`, dan semua seeder di atas dipanggil otomatis oleh `DatabaseSeeder`. Jadi tidak perlu mengedit `routes/web.php` atau `DatabaseSeeder`. (File modul 5 datang dari branch Intan.)

- Migration: awalan tanggal per modul, modul 1 = `2026_11_01_*`, modul 2 = `2026_11_02_*`, dst. (supaya urutan FK aman). `peran` dan `pengguna` = `2026_10_30_*`.

## Aturan keras
1. Hanya sentuh file modul milik pengguna Claude Code ini. File bersama (layout, `routes/web.php`, `DatabaseSeeder`, model `Pengguna`, middleware) milik Nug. Butuh perubahan di situ → bilang ke manusia, jangan edit sendiri.
2. Migration yang sudah di-merge ke `main` JANGAN diedit. Mau ubah tabel → bikin migration baru.
3. Jangan commit `.env`, `vendor/`, `node_modules/`.
4. Jangan push ke `main`, jangan force push.
5. Jangan hapus atau ubah data seeder modul lain.

## Tampilan (struktur ikut Figma, gaya ikut FoodLoop)
Mockup Figma berupa wireframe abu-abu. Dari mockup, tiru **struktur, menu, dan isi**-nya. Untuk **warna, huruf, dan bentuk**, pakai gaya FoodLoop yang sudah jadi di `public/css/foodloop.css`: latar krem hangat, hijau gelap dari logo sebagai warna utama, garis tipis lembut, sudut agak membulat. Tanggal, angka, dan kode tampil seperti cetakan label rak.
- Layout dashboard: sidebar kiri 240px (logo + menu sesuai peran; menu yang modulnya belum ada dikumpulkan di bawah "Segera hadir"), topbar (tanggal dan jam, Notifikasi bila modulnya ada, avatar + dropdown Profil saya / Keluar), konten di kanan.
- Halaman login: dua kolom, kiri slogan, kanan kartu form. Halaman daftar: satu kartu form di tengah.
- Pakai layout bersama `layouts/app.blade.php` (milik Nug), jangan bikin layout sendiri.
- Teks dan isi contoh di Figma (mis. "Warung Bu Sari") hanya contoh, data asli dari database.

### Aturan gaya (wajib)
1. **Jangan menulis warna, bayangan, ukuran huruf, atau `style="..."` berisi warna di view.** Jangan menambah `<style>`, file CSS, font, atau CDN sendiri (termasuk Tailwind).
2. Pakai kelas Bootstrap 5 biasa atau komponen di bawah. Kelas Bootstrap (`btn`, `card`, `table`, `badge`, `alert`, `form-control`, `modal`) sudah otomatis bertema FoodLoop.
3. Butuh gaya atau komponen yang belum ada? Kabari Nug supaya ditambahkan di `foodloop.css`, jangan bikin sendiri.
4. Satu tombol utama (`btn-utama`, hijau gelap) per layar. Oranye (`btn-aksen`) hanya untuk ajakan terpenting.
5. Status selalu ditampilkan lewat `<x-status>`, jangan memilih warna badge sendiri. Kuning hanya muncul lewat cap status yang menunggu tindakan (lihat daftar warna status di bawah).
6. Huruf mono kapital (`fl-cap`, `fl-data`) hanya untuk keterangan kecil, tanggal, angka, dan kode. Judul halaman otomatis memakai huruf judul; judul panel dan bagian form memakai huruf biasa.
7. Ikon hanya lewat `<x-ikon>`, jangan pakai emoji, tanda "+" sebagai ikon, atau ikon dari pustaka lain.

### Komponen tampilan
Contoh hidup semua komponen: buka `/komponen` setelah login (hanya di `APP_ENV=local`).

| Kebutuhan | Cara pakai |
|---|---|
| Judul halaman + tombol aksi | `<x-kepala-halaman judul="Titik Pengambilan"><a href="..." class="btn btn-utama"><x-ikon nama="tambah" />Tambah titik</a></x-kepala-halaman>` |
| Judul + tautan kembali | `<x-kepala-halaman judul="Tambah titik" :kembali="route('...')" teks-kembali="Kembali ke daftar titik" />` |
| Kotak putih berjudul | `<x-panel judul="Daftar titik">...</x-panel>`, tombol di kanan judul lewat `<x-slot:aksi>...</x-slot:aksi>` |
| Kotak putih polos | `<x-panel>...</x-panel>` atau `<div class="fl-panel">` atau `<div class="card">` |
| Angka ringkasan berikon | `<div class="fl-statistik-baris"><x-statistik label="Listing aktif" :angka="$n" ikon="kotak" /> ...</div>` (`:angka` null = modul pemilik datanya belum dibuat, tampil "Segera hadir"; angka 0 tetap 0; opsional `catatan="..."`) |
| Badge status | `<x-status :nilai="$listing->status_listing" />` (warna otomatis dari status baku) |
| Daftar kosong | `<x-kosong judul="Belum ada listing" ikon="kotak">Kalimat arahan.<x-slot:aksi>tombol</x-slot:aksi></x-kosong>` (ikon opsional). Judulnya jujur: "Belum ada listing" kalau memang kosong, "Segera hadir" kalau modulnya belum dibuat. |
| Ikon | `<x-ikon nama="lokasi" />`. Nama: tambah, kotak, sepeda, unggah, dokumen, masuk, jam, daun, perisai, toko, bendera, rute, centang, hati, kalender, lokasi, lonceng, orang, buku, cari |
| Keterangan kecil / angka | `<span class="fl-cap">Sisa porsi</span>`, `<span class="fl-data">FL-7K2Q</span>` |
| Pesan sukses/gagal | `@include('partials.pesan')` + controller `->with('sukses', '...')` / `->with('galat', '...')` |
| Tabel | `<div class="fl-tabel"><table class="table">...</table></div>`; sel tanggal, jam, dan kode pakai `<td class="fl-data">` |
| Tab saring | `<nav class="fl-saring"><a href="..." class="aktif" aria-current="page">Menunggu <span class="fl-saring-jumlah">3</span></a> ...</nav>` |
| Rincian (keterangan: nilai) | `<dl class="fl-rincian"><dt>Nama usaha</dt><dd>...</dd></dl>` |
| Linimasa riwayat | `<ol class="fl-linimasa"><li class="positif/negatif/tunggu"><span class="fl-linimasa-judul">..</span><span class="fl-linimasa-waktu">..</span><span class="fl-linimasa-ket">..</span></li></ol>` |
| Pratinjau berkas | `<img class="fl-pratinjau">` atau `<iframe class="fl-pratinjau">` lalu `<div class="fl-pratinjau-berkas">` |
| Ikon di daftar baris | `<span class="fl-daftar-gambar fl-daftar-ikon"><x-ikon nama="toko" /></span>` |
| Kartu isi selebar form | `<x-panel class="fl-kartu-isi">` (lebar maks. 760px, untuk form dan halaman detail) |
| Label field opsional | `<label class="form-label">Patokan <span class="fl-opsional">opsional</span></label>` |
| Kelompok isian berjudul | `<fieldset class="fl-koordinat"><div class="fl-koordinat-kepala"><legend class="form-label mb-0">Koordinat</legend> tombol</div> ... </fieldset>`, label di dalamnya `form-label fl-label-kecil` |
| Isian angka/koordinat | `<input class="form-control fl-data" inputmode="decimal">` (placeholder otomatis huruf biasa) |
| Judul baris yang bisa diklik | `<a class="fl-daftar-judul fl-tautan-judul">` |
| Tautan ber-ikon di samping nilai | `<a class="fl-tautan-peta"><x-ikon nama="lokasi" />Lihat di OpenStreetMap</a>` |
| Panel yang butuh perhatian | `<x-panel judul="Pengajuan ditolak" class="fl-panel-perhatian">` (garis merah di atas, mis. penolakan yang harus ditindaklanjuti) |
| Kutipan alasan/catatan | `<p class="fl-kutipan">{{ $alasan }}</p>` (mis. alasan penolakan dari Admin) |
| Langkah bernomor | `<ol class="fl-langkah-urut"><li>...</li></ol>` (petunjuk urut di dalam panel) |
| Konfirmasi hapus | contoh lengkap: `resources/views/akun_mitra/titik/_modal-hapus.blade.php` (satu modal untuk semua baris lewat `data-nama` dan `data-aksi`) |
| Pilihan bertumpuk / negatif | tambah `fl-pilihan-tumpuk` pada `.fl-pilihan`; `fl-pilihan-bahaya` pada kartu yang berakibat negatif (merah saat dipilih) |
| Daftar baris (gambar, judul, aksi) | `<ul class="fl-daftar"><li>` berisi `fl-daftar-gambar`, `fl-daftar-isi` (`fl-daftar-judul`, `fl-daftar-ket`), `fl-daftar-aksi` |
| Form | `form-label` di atas `form-control` / `form-select`; salah: kelas `is-invalid` + `<div class="invalid-feedback">`; bantuan: `form-text` |
| Judul bagian form | `<h2 class="fl-form-bagian">Data usaha</h2>` |
| Pilihan berupa kartu | `<div class="fl-pilihan">` berisi `<label class="fl-pilihan-kartu"><input type="radio">` + `fl-pilihan-ikon`, `fl-pilihan-judul`, `fl-pilihan-ket` |
| Unggah berkas | `<label class="fl-unggah"><input type="file">` + `fl-unggah-ikon`, `fl-unggah-judul`, `fl-unggah-ket`; nama berkas tampil otomatis |
| Kata sandi + Lihat | `<div class="fl-gabung"><input type="password" id="x" class="form-control"><button type="button" class="fl-lihat-sandi" aria-controls="x">Lihat</button></div>` |
| Tombol kirim memproses | `<button type="submit" data-memproses="Menyimpan">` (cegah kirim ganda otomatis) |
| Tombol akhir form | `<div class="fl-form-aksi">` berisi Batal (`btn-sekunder`) lalu Simpan (`btn-utama`) |

Tombol:
| Kelas | Untuk |
|---|---|
| `btn btn-utama` (sama: `btn-primary`, `btn-dark`) | aksi utama, hijau gelap: Simpan, Tambah, Setujui |
| `btn btn-sekunder` (sama: `btn-outline-secondary`, `btn-light`) | Batal, Ubah, Lihat semua |
| `btn btn-aksen` | oranye, ajakan terpenting di halaman, mis. Ajukan permintaan |
| `btn btn-bahaya` / `btn btn-outline-danger` | Tangguhkan, Tolak / Hapus |
| tambah `btn-sm` | tombol kecil di baris tabel atau daftar |

Warna status di `<x-status>`: hijau (Aktif, Terverifikasi, Disetujui, Selesai, Diserahkan, Terbit, Berlangsung, Ditindaklanjuti, Berhasil), kuning (Menunggu, Menunggu verifikasi, Menunggu Persetujuan, Dijadwalkan, Baru, Ditinjau, Akan Datang, Draf), oranye (Dalam pengantaran), merah (Ditolak, Ditangguhkan, Gagal), krem abu (Ditutup, Habis, Kedaluwarsa, Dibatalkan, Diarsipkan).

Acuan desain lengkap (warna, huruf, bentuk, komponen): `DESIGN.md`. Fakta produk untuk desain: `PRODUCT.md`.

## Cara kerja yang diminta
- Mulai dengan **rencana singkat** (file apa yang dibuat/diubah), tunggu manusia setuju, baru nulis kode.
- Kerjakan satu PBI sekali jalan. Kriteria selesai ada di kartu Jira (FOOD-xx) dan DoD proposal 5.5.
- Setelah selesai: jalankan `php artisan migrate:fresh --seed` dan cek halaman di browser, lalu ringkas apa yang dibuat.
- Commit kecil dengan pesan jelas, contoh: `PBI-04: form buat listing donasi`.

## Menjalankan proyek
```
composer install
cp .env.example .env        # lalu isi DB_DATABASE, DB_USERNAME, DB_PASSWORD (MySQL XAMPP)
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

### Akun contoh (khusus pengembangan lokal)
Dibuat oleh `AkunContohSeeder` saat `php artisan migrate:fresh --seed`, hanya jika `APP_ENV=local`. Jangan dipakai di server.

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | `admin@foodloop.test` | `password` |
| Mitra/Donatur | `mitra@foodloop.test` | `password` |
| Penerima | `penerima@foodloop.test` | `password` |
| Relawan | `relawan@foodloop.test` | `password` |

Mitra contoh lain untuk halaman Verifikasi Mitra (kata sandi juga `password`): `bakery.rotikita@`, `katering.nusantara@`, `kantin.fri@` (Menunggu verifikasi), dan `panitia.festival@foodloop.test` (Ditolak, beserta alasannya). `mitra@foodloop.test` (Warung Bu Sari) sudah Terverifikasi.

### Catatan auth
- Model login = `App\Models\Pengguna` (tabel `pengguna`, PK `id_pengguna`, sandi di kolom `kata_sandi` dan di-hash otomatis). Peran = `App\Models\Peran`.
- `auth()->id()` mengembalikan `id_pengguna`. Cek peran: `auth()->user()->punyaPeran(Peran::ADMIN)`.
- Akun berstatus Ditangguhkan otomatis dikeluarkan (middleware `PastikanAkunAktif`).
- Timezone aplikasi dan koneksi MySQL = `Asia/Jakarta` (WIB).
- Halaman masuk: `/masuk` (route `login`), keluar: `POST /keluar` (route `logout`), dashboard: `/dashboard` (route `dashboard`).
- Daftar Penerima/Relawan: `/daftar` (route `register`). Daftar mitra: `/daftar/mitra` (route `register.mitra`). Peran selalu ditetapkan server; `id_peran` tidak pernah diterima dari form.
- Data usaha mitra: `auth()->user()->mitra` (model `App\Models\Mitra`, tabel `mitra`). Cek boleh membuat listing: `auth()->user()->mitra?->terverifikasi()`.
- Alamat mitra disimpan satu kolom `alamat` (sesuai ERD). Bagian-bagiannya: `$mitra->alamat_jalan`, `$mitra->kota`, `$mitra->kode_pos`; menyimpan: `Mitra::gabungAlamat($jalan, $kota, $kodePos)`.
- Jenis mitra (ERD 4.3.1): `Mitra::JENIS` = Kantin, Restoran, Toko roti, Katering, Panitia acara.
- Dokumen verifikasi mitra ada di disk privat `local` (`storage/app/private/verifikasi_mitra`), hanya untuk Admin.
- Verifikasi mitra oleh Admin: `/verifikasi-mitra` (route `verifikasi-mitra.index`), tinjau `verifikasi-mitra.show`. Keputusan lewat `$pengajuan->setujui($admin)` / `$pengajuan->tolak($admin, $alasan)`; keduanya ikut mengubah `mitra.status_mitra`.
- Profil Mitra (PBI-02): `/profil-mitra` (route `profil-mitra.edit`, simpan `profil-mitra.update`), dijaga `auth` + `peran:Mitra/Donatur`. Mengubah profil lewat `$mitra->ubahProfilMitra($data)` hanya mengubah nama, jenis, dan alamat; status verifikasi tidak berubah (FR-02).
  - Aturan validasi data usaha dipakai bersama form daftar mitra dan Profil Mitra: `Mitra::aturanDataUsaha()` + `Mitra::pesanDataUsaha()`, dokumen `Mitra::aturanDokumen($wajib)` + `Mitra::pesanDokumen()`.
  - Mitra berstatus Ditolak memperbaiki data lalu mengajukan ulang di halaman yang sama (`POST profil-mitra.ajukan-ulang`). Dokumen baru opsional; kalau kosong, dokumen pengajuan terakhir dipakai lagi. Pengajuan dibuat lewat `VerifikasiMitra::buatPengajuan($mitra, $path)` (hanya dari status Ditolak), status kembali Menunggu verifikasi, dan riwayat pengajuan lama tetap ada untuk Admin.
- **Route yang hanya boleh dipakai mitra terverifikasi** (mis. membuat listing): `->middleware(['auth', 'peran:Mitra/Donatur', 'mitra.terverifikasi'])`. Mitra yang belum terverifikasi dialihkan ke dashboard dengan pesan.
- Titik pengambilan (PBI-03): `/titik-pengambilan` (route `titik-pengambilan.index`), dijaga `auth` + `peran:Mitra/Donatur` saja (mitra belum terverifikasi tetap boleh, sesuai use case 4.1.3). Model `App\Models\TitikPengambilan` (tabel `titik_pengambilan`, PK `id_titik`).
  - Pilihan titik di form modul lain (listing Ica, pickup Bitha): `auth()->user()->mitra->titikPengambilan()->orderBy('nama_titik')->get()`; validasi pilihan: `Rule::exists('titik_pengambilan', 'id_titik')->where('id_mitra', $idMitra)`.
  - Titik tidak bisa dihapus selama dipakai listing berstatus `Aktif` (`$titik->cekTitikDipakaiListing()`). Pengecekan ini membaca tabel `listing` kolom `id_titik` dan `status_listing`, jadi pakai nama kolom itu persis seperti ERD.
  - Tampilan: `$titik->koordinat` ("-6.8915, 107.6159") dan `$titik->url_peta` (tautan OpenStreetMap).
- Format waktu Indonesia: `App\Support\Waktu::relatif($tgl)` ("hari ini", "kemarin", "3 hari lalu"), `Waktu::lengkap($tgl)` ("11 Nov 2026, 08.15"), `Waktu::tanggal($tgl)`.
- Halaman wajib login: `->middleware('auth')`. Batasi peran: `->middleware(['auth', 'peran:Admin'])`, `'peran:Mitra/Donatur'`, atau beberapa sekaligus `'peran:Penerima,Relawan'`.

### Catatan layout
- View modul: `@extends('layouts.app')` lalu `@section('content')`. Opsional: `@section('title', '...')`, `@push('scripts')`.
- Pesan sukses/gagal: taruh `@include('partials.pesan')` di view, lalu dari controller `->with('sukses', '...')` atau `->with('galat', '...')`.
- Komponen dan aturan gaya: lihat bagian "Tampilan" di atas.
- Menu sidebar diatur di `app/Support/MenuSidebar.php` (milik Nug). Menu otomatis aktif begitu route dengan nama yang tercantum di sana terdaftar. Pakai nama route itu, atau kabari Nug kalau mau nama lain.
