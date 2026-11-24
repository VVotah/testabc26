# Panduan Tim FoodLoop

Baca sekali, simpan. Cuma 5 menit. Kalau bingung, tanya Nug di grup, jangan nebak.

## 1. Gambaran besar
- Kita bikin FoodLoop pakai Laravel + MySQL + Bootstrap 5.
- Satu orang = satu modul = file sendiri-sendiri. Itu kunci biar kodenya nggak tabrakan.
- Semua tugas ada di Jira project FOOD, sudah ada namamu di kartunya.
- Referensi: proposal (`docs/Proposal PPL_V01_Kelompok A-A Baru.pdf`) dan Figma (`docs/mockup/mockup_README.md`).
- Sprint 1: 26 Okt - 22 Nov. Sprint 2: 23 Nov - 27 Des.

## 2. Tugas Minggu 1 (9 - 15 Nov) dan Minggu 2 (16 - 22 Nov)

| Siapa | Kerjain duluan (minggu 1) | Lanjut (minggu 2) |
|---|---|---|
| Nug | Layout + login + registrasi (FOOD-30, 31, 32), PBI-03 (FOOD-11) | Verifikasi mitra (FOOD-33), PBI-02 (FOOD-10) |
| Ica | PBI-04 Listing (CRUD + pencarian) (FOOD-12) | PBI-05 (FOOD-13) |
| Nico | PBI-07 Permintaan (FOOD-15) | PBI-08 (FOOD-16) |
| Bitha | PBI-10 (FOOD-18) | PBI-12 (FOOD-20) |
| Intan | PBI-13 (FOOD-21) | PBI-14 (FOOD-22) |
| Rhein | PBI-16 (FOOD-24) | PBI-17 (FOOD-25) |
| Clara | PBI-21 Notifikasi (FOOD-29), kerjain ini dulu karena dipakai modul lain | PBI-20 (FOOD-28) |

Senin 9 Nov: clone repo, setup, bikin branch, baca CLAUDE.md dan Figma.
Selasa 10 Nov: Nug merge layout + login. Sebelum itu, mulai dari migration, model, seeder, controller (belum perlu login).
Rabu - Sabtu: ngerjain PBI pertama, buka Pull Request kalau sudah selesai.
Target: kedua PBI Sprint 1 kamu sudah masuk `main` sebelum 22 Nov.

## 3. Aturan Git (ikutin urut, jangan lompat)

**Setup awal (sekali saja)**
```
git clone <link repo GitHub>
cd foodloop
git checkout -b namamu        # contoh: git checkout -b intan
git push -u origin namamu
```
Satu orang = satu branch. Nama branch bebas sesuai maumu (contoh yang sudah ada: `nugraha`, `intan`, `WWWotah`), asal dipakai terus dan tidak diganti-ganti. Di panduan ini `namamu` = nama branch kamu.

**Tiap mau mulai kerja (WAJIB, tiap hari)**
```
git checkout namamu
git pull origin main          # ambil kerjaan terbaru teman
```

**Kerja, lalu simpan**
```
git add .
git commit -m "PBI-04: form buat listing donasi"
git push origin namamu
```
Commit sering-sering, kecil-kecil. Lebih aman daripada 1 commit raksasa.

**Selesai satu PBI**
1. `git pull origin main`, kalau ada bentrok, beresin dulu atau minta tolong Nug.
2. Cek jalan: `php artisan migrate:fresh --seed`, buka halamannya.
3. Buka Pull Request di GitHub: dari `namamu` ke `main`. Judul: `PBI-04 Buat listing donasi`. Tulis nomor Jira (FOOD-12).
4. Minta 1 teman me-review. Nug yang merge.
5. Setelah di-merge: `git checkout namamu` lalu `git pull origin main`.

**Jangan pernah**
- push langsung ke `main`
- `git push --force`
- edit file modul orang lain (layout, `routes/web.php`, `DatabaseSeeder`, model `Pengguna` punya Nug)
- edit migration yang sudah masuk `main` (bikin migration baru kalau mau ubah tabel)
- commit `.env`, `vendor/`, `node_modules/`

Kalau butuh perubahan di file orang lain, chat orangnya, jangan edit sendiri.

## 4. Biar kodenya nggak konflik
- Route: tulis di `routes/modul/<modulmu>.php` saja.
- Controller, view, seeder: di folder/file modulmu.
- Migration: awalan tanggal modulmu (modul 1 = `2026_11_01_...`, modul 2 = `2026_11_02_...`, dst).
- Nama tabel dan kolom: persis seperti ERD proposal Bab 4.3.
- Jalankan `git pull origin main` tiap pagi.

## 5. Biar tampilannya mirip Figma
- Buka frame Figma halamanmu (daftar di `docs/mockup/mockup_README.md`).
- Figma kita masih wireframe abu-abu dan hanya rujukan, jadi dari Figma cukup tiru **susunan dan isinya** (sidebar kiri, topbar, kartu, form label di atas); tampilan asli boleh sedikit berbeda.
- Pakai Bootstrap 5 standar. Pakai layout bersama dari Nug (`layouts/app.blade.php`), jangan bikin layout sendiri.
- Warna, huruf, dan bentuk sudah jadi di `public/css/foodloop.css` (latar krem, hijau gelap dari logo, garis tipis, sudut membulat). Jangan nulis warna atau `<style>` sendiri di view: pakai kelas Bootstrap biasa dan komponen bersama (`<x-kepala-halaman>`, `<x-panel>`, `<x-statistik>`, `<x-status>`, `<x-kosong>`, `<x-ikon>`). Daftar lengkapnya di `CLAUDE.md` bagian "Komponen tampilan", contoh hidupnya di halaman `/komponen` setelah login. Butuh komponen baru? Chat Nug.
- Export frame Figma-mu jadi PNG ke `docs/mockup/<modul>/` biar Claude Code bisa lihat.

## 6. Pakai Claude buat ngoding (Claude biasa atau Claude Code)
Claude biasa nggak bisa lihat repo-mu dan nggak ingat chat lama. Jadi **tiap bikin chat baru, upload dulu berkas ini, urut**:
1. `CLAUDE.md` (aturan teknis)
2. `docs/KONTEKS_PROYEK.md` (tim, 21 PBI, tabel, istilah)
3. `PANDUAN_TIM.md` (ini)
4. `docs/mockup/mockup_README.md` + PNG frame Figma halamanmu
5. `docs/Proposal PPL_V01_Kelompok A-A Baru.pdf` (ERD Bab 4.3 ada kolom dan tipe datanya)

Lalu kirim prompt ini (ganti yang dalam kurung):
```
Aku [nama], pemilik modul [nomor + nama]. Kerjain [PBI-xx] (kartu [FOOD-xx]).
Ikuti semua aturan di berkas yang kuupload. Jangan nambah fitur/tabel di luar proposal.
Kasih rencana dulu (file apa saja yang dibuat/diubah), tunggu aku setuju.
Setelah itu kasih kode per file, tulis path lengkapnya (contoh: app/Http/Controllers/Listing/ListingController.php).
```
Cara pakainya:
- Claude kasih kode per file, kamu salin ke folder repo lokal (VS Code) sesuai path yang disebut, lalu jalankan dan tes.
- Ada error? Tempel pesan error + isi file yang bermasalah ke chat yang sama.
- Cek hasilnya sendiri sebelum commit. Kalau Claude mau nambah package atau ubah file orang lain (layout, `routes/web.php`, `DatabaseSeeder`, model `Pengguna`), tolak dan tanya Nug.
- Satu chat = satu PBI. Chat sudah panjang dan mulai ngaco? Bikin chat baru, upload ulang berkas di atas.
- Yang pakai Claude Code: cukup buka folder repo, `CLAUDE.md` terbaca otomatis.

## 7. Jira dan laporan harian
- Geser kartu: To Do → In Progress (pas mulai) → In Review (pas buka PR) → Done (setelah merge).
- Selesai = jalan di browser + ada di `main` + sesuai kriteria kartu (Definition of Done proposal 5.5).
- Laporan harian di Slack, minimal 3x seminggu (Senin - Sabtu, sebelum 09.00 WIB):
  `/report kemarin ngerjain apa | hari ini mau apa | kendala`

## 8. Stuck?
Chat Nug di grup, sebutkan: kartu Jira, apa yang sudah dicoba, pesan error-nya. Jangan diam lebih dari sehari.
