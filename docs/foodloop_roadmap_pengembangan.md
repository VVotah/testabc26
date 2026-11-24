# FoodLoop: peta jalan pengembangan (disusun 7 Nov 2026)
Lanjutan dari foodloop_erd_update.md dan foodloop_status_revisi.md. Sumber utama: proposal yang sudah dikumpul (Bab 4.3 ERD, 4.5 mockup, Bab 5 Scrum). Kalau ada beda dengan proposal, proposal yang menang.

## Fakta dari proposal (jangan diubah)
- Jadwal: 2 Sprint. Sprint 1 = minggu ke-7 sampai ke-10 (4 minggu). Sprint 2 = minggu ke-11 sampai ke-15 (5 minggu). Tanggal pasti tiap minggu belum diketahui, tanya Nug minggu ke berapa sekarang.
- Tiap anggota: 2 PBI di Sprint 1, 1 PBI di Sprint 2 (total 3).
- Sprint 1 (14 PBI): 02, 03, 04, 05, 07, 08, 10, 12, 13, 14, 16, 17, 20, 21. Goal: fitur inti dan alur pendukung tersedia.
- Sprint 2 (7 PBI): 01, 06, 09, 11, 15, 18, 19. Goal: fitur pelengkap selesai dan seluruh fitur diuji.
- Stack (4.2): PHP + JavaScript, Laravel (MVC monolit 3 tier), Bootstrap atau Tailwind, MySQL, file foto/dokumen di storage lokal server, Nginx/Apache di Cloud VPS (2 vCPU, 4 GB, 50 GB SSD), Git + GitHub, Trello/Jira, Figma.
- Definition of Done (5.5), 7 poin: sesuai kriteria PBI; terintegrasi tanpa error mengganggu; diuji (input valid dan error); integrasi antarmodul dicek; tanpa bug kritis; dokumentasi/petunjuk diperbarui; ditinjau tim dan bisa didemokan di Sprint Review.
- Event Scrum (5.4): Sprint Planning, Daily Scrum, Sprint Review, Sprint Retrospective.
- Registrasi, login, verifikasi mitra bukan PBI tapi WAJIB ada supaya semua PBI bisa dites (fungsi pendukung).

## Pembagian PBI per anggota
| Anggota | Modul (Bab 4.3) | Sprint 1 (urutan kerja) | Sprint 2 |
|---|---|---|---|
| Nug | 4.3.1 Akun dan Mitra | 03 titik pengambilan, lalu 02 profil usaha | 01 profil akun |
| Ica (Meilisya) | 4.3.2 Listing Donasi | 04 listing, lalu 05 jadwal surplus | 06 kedaluwarsa/habis |
| Nico | 4.3.3 Pencarian dan Permintaan | 07 permintaan, lalu 08 ikuti mitra | 09 peta radius |
| Bitha | 4.3.4 Pickup dan Logistik | 10 jadwal pickup, lalu 12 kode serah terima | 11 tugas relawan |
| Intan | 4.3.5 Forum Sharing | 13 postingan, lalu 14 klaim/komentar/lapor | 15 aktivitas forum saya |
| Rhein | 4.3.6 Edukasi dan Dampak | 16 artikel, lalu 17 kampanye | 18 laporan dampak (PDF) |
| Clara | 4.3.7 Moderasi dan Kualitas | 21 notifikasi (service dulu), lalu 20 ulasan | 19 tindak lanjut laporan |

## Fase kerja
### Fase 0: Fondasi dan persiapan (sekarang sampai awal Sprint 1, sekitar 1 minggu)
Nug (fondasi, satu orang supaya repo tidak bentrok):
1. Layout dasar Blade per peran (Admin, Mitra/Donatur, Penerima, Relawan) mengikuti mockup Figma, plus komponen umum (alert, tombol, tabel).
2. Registrasi (Penerima, Relawan, Mitra), login, logout, middleware peran. Kolom kata_sandi dipakai sebagai password (override getAuthPassword), model User diganti ke tabel pengguna.
3. Verifikasi mitra oleh Admin (pengajuan, setujui/tolak), karena Mitra harus terverifikasi sebelum bisa membuat listing.
4. Tulis CLAUDE.md di akar repo (stack, aturan penamaan, struktur folder per modul, cara menjalankan, link proposal dan Figma, aturan Git).
5. Siapkan Jira, branch protection, template PR.
Semua anggota lain (paralel):
1. Environment jalan di lokal (clone, composer install, .env, php artisan serve).
2. Rancangan tabel modul sendiri (tipe data) dari Bab 4.3, lalu bikin migrasi + seeder dummy modul sendiri di branch sendiri.
3. Baca ulang skenario (4.1), mockup (4.5), dan PBI sendiri (tabel 5.2).

### Sprint 1 (minggu ke-7 sampai 10)
- Minggu 1: Sprint Planning (konfirmasi PBI, kriteria penerimaan, urutan). Migrasi + model + route modul sendiri. PBI pertama mulai.
- Minggu 2: PBI pertama jalan end-to-end (route, controller, view sesuai mockup), PR pertama dimerge.
- Minggu 3: PBI kedua jalan, mulai integrasi dengan modul lain.
- Minggu 4: integrasi, pengujian, perbaikan bug, Sprint Review dan Retrospective.

### Sprint 2 (minggu ke-11 sampai 15)
- Minggu 1: Sprint Planning, perbaiki sisa bug Sprint 1, mulai PBI Sprint 2.
- Minggu 2-3: PBI Sprint 2 selesai dan dimerge.
- Minggu 4: uji alur lengkap end-to-end, rapikan tampilan agar sesuai mockup.
- Minggu 5: deploy ke VPS (bila diwajibkan), dokumentasi/petunjuk penggunaan, Sprint Review akhir, Retrospective, finalisasi.

Alur uji end-to-end: Mitra registrasi, Admin verifikasi, Mitra buat titik pengambilan, Mitra buat listing, Penerima cari dan ajukan permintaan, Mitra setujui (stok berkurang), jadwal pickup, Relawan ambil tugas, serah terima dengan kode, ulasan, notifikasi, laporan dampak.

## Aturan kerja bersama
Git:
- main = stabil (hanya lewat PR). Satu branch per orang (ikut PANDUAN_TIM.md), nama branch bebas sesuai keinginan anggota, contoh: nugraha, intan, WWWotah. Semua PBI milik anggota itu dikerjakan di branch-nya; supaya tidak bentrok, pull main tiap mulai kerja dan buka PR tiap satu PBI selesai.
- Commit kecil dan sering, PR wajib direview minimal 1 orang, Nug yang merge. Pull main sebelum mulai kerja dan sebelum PR.
Migrasi:
- Urutan migrasi harus mengikuti ketergantungan FK. Pakai prefiks tanggal per modul: modul 1 = 2026_11_01_xxxxxx, modul 2 = 2026_11_02_xxxxxx, dan seterusnya sampai modul 7, supaya php artisan migrate:fresh selalu berurutan benar. Dalam satu modul, tabel induk dibuat dulu (mis. kategori_makanan sebelum listing).
- Nama tabel dan kolom persis seperti ERD 4.3 (bahasa Indonesia, snake_case). Jangan ganti tanpa kabar grup.
Data dummy:
- Tiap modul punya seeder sendiri dan didaftarkan di DatabaseSeeder, jadi tiap orang bisa tes tanpa menunggu modul lain.
Ketergantungan lintas modul:
- Notifikasi: Clara bikin NotifikasiService (kirim notifikasi ke pengguna) di minggu pertama Sprint 1, Nico dan Bitha memanggilnya saat status permintaan/pickup berubah.
- Listing butuh titik pengambilan (Nug 03 harus duluan). Permintaan butuh listing. Pickup butuh permintaan dan titik. Ulasan dan laporan dampak butuh pickup/permintaan selesai.
UI:
- Satu framework untuk semua: Bootstrap 5 (CDN, tanpa build) dengan warna dan font dari design system FoodLoop (lihat areas/foodloop-design). Jangan campur dengan Tailwind.
Peta (PBI-09): Leaflet + OpenStreetMap (gratis, tanpa API key). Belum ada di daftar teknologi 4.2, tambahkan di catatan tim.
PDF (PBI-18): paket barryvdh/laravel-dompdf.
Foto/dokumen: storage lokal Laravel (php artisan storage:link).

## Jira (TEK-10)
- Satu project, 21 PBI sebagai Story (isi dari tabel 5.2), 2 Sprint sesuai tabel jadwal, assignee sesuai pembagian, subtask per langkah (migrasi, model, controller, view, tes).
- Status: To Do, In Progress, In Review, Done. Done hanya jika DoD terpenuhi.

## Slack dan ritme tim
- Daily report /report minimal 3x per minggu per orang (Senin sampai Sabtu, sebelum 09.00 WIB), isi progres PBI nyata.
- Daily Scrum cukup async di grup: kemarin, hari ini, kendala.
- Satu sinkronisasi singkat per minggu (30 menit, online). Sprint Review dan Retrospective di akhir tiap sprint.

## Cara kerja dengan Claude Code (yang ngoding)
Tiap anggota buka repo di Claude Code, jalankan di branch miliknya sendiri, lalu beri: teks PBI (tabel 5.2), skenario (4.1.x), ERD (4.3.x), kelas (4.4.x), mockup (4.5.x, Figma file key GKIIdmZGblx9he844xJCYF). Minta Claude Code membuat rencana dulu, lalu migrasi, model, controller, view, tes, commit per langkah. Setelah jadi, jalankan fitur dan cek terhadap kriteria PBI dan DoD sebelum PR.

## Pertanyaan terbuka
- Sekarang minggu ke berapa (supaya tanggal Sprint 1 dan 2 bisa dipetakan)?
- Apakah deploy ke VPS wajib, dan apa yang dikumpulkan di akhir (laporan, demo, repo)?
- Verifikasi mitra dikerjakan Nug (usulan) atau dibagi ke Clara?
- Setuju Bootstrap 5 + Leaflet/OSM + Jira?
