# Konteks Proyek FoodLoop (untuk Claude Code dan anggota tim)

Berkas ini ringkasan "siapa mengerjakan apa" dan istilah proyek. Detail tabel (kolom + tipe data) ada di **proposal Bab 4.3 (ERD)**; kalau berbeda dengan berkas ini, proposal yang menang. Status kerja dan kriteria selesai ada di Jira project FOOD.

## Tentang aplikasi
- **FoodLoop**: aplikasi web redistribusi dan sharing makanan berlebih (SDG 12, target 12.3 dan 12.5). Skala umum: kantin, restoran, toko roti, katering, panitia acara, individu.
- Alur inti: Mitra daftar, Admin verifikasi, Mitra buat titik pengambilan dan listing, Penerima cari dan ajukan permintaan, Donatur setujui (stok berkurang), jadwal pickup, Relawan antar bila perlu, serah terima dengan kode pickup, ulasan, notifikasi, laporan dampak. Selain itu ada forum sharing untuk individu, edukasi, dan moderasi.
- Metode Scrum, 2 Sprint. Sprint 1: 26 Okt - 22 Nov 2026. Sprint 2: 23 Nov - 27 Des 2026.
- **Jangan menambah fitur di luar 21 PBI** (plus registrasi, login/logout, dashboard, verifikasi mitra sebagai fungsi pendukung).

## Aktor
Admin; Mitra/Donatur (satu aktor, "Donatur" saat menyalurkan makanan); Penerima; Relawan; "Pengguna" = semua akun yang sudah login; "Sistem" = proses otomatis. Individu yang berbagi di forum memakai akun Penerima. Admin, Mitra/Donatur, Penerima, Relawan = baris di tabel `peran`.

## Tim dan tanggung jawab
| Anggota | Branch | Modul | PBI (Jira) |
|---|---|---|---|
| Nugraha Ade Mulyana (Nug), PM + Scrum Master | nugraha | 1 Akun & Mitra, plus Fondasi (layout, registrasi, login, middleware peran, verifikasi mitra) | 01 (FOOD-9), 02 (FOOD-10), 03 (FOOD-11) |
| Meilisya Nabila Siregar (Ica) | ica | 2 Listing Donasi | 04 (FOOD-12), 05 (FOOD-13), 06 (FOOD-14) |
| Nico Digdaya Agis | WWWotah | 3 Pencarian & Permintaan | 07 (FOOD-15), 08 (FOOD-16), 09 (FOOD-17) |
| Tabitha Glorya Yobelitha Sirait (Bitha) | bitha | 4 Pickup & Logistik | 10 (FOOD-18), 11 (FOOD-19), 12 (FOOD-20) |
| Intan Nurlistiyani | intan | 5 Forum Sharing | 13 (FOOD-21), 14 (FOOD-22), 15 (FOOD-23) |
| Rafly Rheinzeda (Rhein) | rhein | 6 Edukasi & Dampak | 16 (FOOD-24), 17 (FOOD-25), 18 (FOOD-26) |
| Faiza Clara | clara | 7 Moderasi & Kualitas | 19 (FOOD-27), 20 (FOOD-28), 21 (FOOD-29) |

Satu orang = satu branch. Nama branch bebas sesuai keinginan anggota; kolom Branch di atas mengikuti yang sudah dibuat (`nugraha`, `WWWotah`, `intan`), sisanya masih perkiraan sampai anggotanya membuat branch.

Semua anggota Fullstack Developer. Urutan kerja Sprint 1: Nug 03 lalu 02 | Ica 04 lalu 05 | Nico 07 lalu 08 | Bitha 10 lalu 12 | Intan 13 lalu 14 | Rhein 16 lalu 17 | Clara 21 (notifikasi dulu) lalu 20. Sprint 2: 01, 06, 09, 11, 15, 18, 19.

## 21 PBI
| PBI | Isi singkat | Aktor | Sprint |
|---|---|---|---|
| 01 | Lihat dan ubah profil akun (nama) | Pengguna | 2 |
| 02 | Lihat dan ubah profil usaha mitra (nama usaha, jenis, alamat, kota, kode pos) | Mitra | 1 |
| 03 | CRUD titik pengambilan (nama, alamat, patokan, koordinat). Dikerjakan pertama | Mitra | 1 |
| 04 | CRUD listing makanan (foto, porsi, gratis/diskon, harga, kedaluwarsa, titik) + penerima cari dan filter listing | Donatur, Penerima | 1 |
| 05 | CRUD jadwal surplus rutin (hari, jam, estimasi porsi) | Mitra | 1 |
| 06 | Sistem menandai listing Habis/Kedaluwarsa, sembunyikan dari pencarian, simpan riwayat | Sistem | 2 |
| 07 | Penerima ajukan permintaan (porsi, catatan); Donatur setujui/tolak; stok berkurang otomatis | Penerima, Donatur | 1 |
| 08 | Profil mitra, ikuti/berhenti ikuti mitra, notifikasi listing baru | Penerima | 1 |
| 09 | Peta sebaran listing aktif berdasarkan radius (Leaflet + OSM) | Penerima | 2 |
| 10 | Jadwalkan pickup (slot waktu + titik pengambilan); Donatur setujui; batalkan | Penerima, Donatur | 1 |
| 11 | Tugas antar relawan, ambil tugas, ubah status pengantaran (tugas dibuat otomatis setelah jadwal disetujui) | Relawan | 2 |
| 12 | Donatur konfirmasi serah terima dengan kode pickup (Berhasil/Gagal) | Donatur | 1 |
| 13 | CRUD postingan sharing di forum | Pengguna | 1 |
| 14 | Klaim porsi, batalkan klaim, komentar, lapor postingan bermasalah + bukti foto | Pengguna | 1 |
| 15 | Aktivitas forum saya + setujui/tolak klaim masuk | Pengguna | 2 |
| 16 | Artikel edukasi: tulis (Draf), terbitkan (Terbit), arsipkan (Diarsipkan); tanpa hapus | Admin | 1 |
| 17 | Kampanye/event: buat, ubah, batalkan (Dibatalkan); tanpa hapus | Admin | 1 |
| 18 | Laporan dampak pribadi (porsi terselamatkan, estimasi berat) + unduh PDF (dompdf); dihitung dari permintaan selesai | Pengguna | 2 |
| 19 | Admin tindak lanjuti laporan masalah (tangguhkan listing/akun, balas pelapor) | Admin | 2 |
| 20 | Rating dan ulasan donatur setelah pickup selesai | Penerima | 1 |
| 21 | Notifikasi dalam aplikasi saat status permintaan/pickup berubah (layanan dipanggil modul lain: PBI-07, 08, 10) | Pengguna (dikirim Sistem) | 1 |

Detail CRUD tiap PBI ada di deskripsi kartu Jira (FOOD-9 s.d. FOOD-29).

## Tabel database dan pemilik (nama persis seperti ERD 4.3)
| Modul | Tabel |
|---|---|
| 1 Nug | `peran`, `pengguna`, `mitra`, `verifikasi_mitra`, `titik_pengambilan` |
| 2 Ica | `kategori_makanan`, `listing`, `jadwal_surplus` |
| 3 Nico | `permintaan`, `pengikut_mitra` |
| 4 Bitha | `jadwal_pickup` (FK `id_titik`, `id_permintaan`), `tugas_pengantaran`, `konfirmasi_serah_terima` |
| 5 Intan | `postingan_sharing`, `klaim_porsi`, `komentar` |
| 6 Rhein | `artikel`, `kampanye` (laporan dampak = agregasi, bukan tabel) |
| 7 Clara | `laporan_masalah`, `tindak_lanjut`, `ulasan`, `notifikasi` |

Catatan penting:
- Nama kelas boleh PascalCase (`Listing` untuk tabel `listing`, kelas di diagram bernama ListingMakanan), tapi **nama tabel dan kolom snake_case Indonesia tunggal**.
- `jadwal_surplus` (Ica) dan `jadwal_pickup` (Bitha) beda tabel, PK-nya `id_jadwal_surplus` dan `id_jadwal_pickup`.
- `ulasan` mengacu ke `id_jadwal_pickup` (relasi 1:1).
- `laporan_masalah` dan `notifikasi` mengacu ke objek lain secara polimorfik (jenis objek + id objek), bukan FK biasa. Bukti foto laporan wajib untuk postingan sharing, maks 5 MB.
- Tabel `pengguna`: `id_pengguna`, `id_peran`, `nama`, `email` (unik), `kata_sandi`, `status_akun` (Aktif/Ditangguhkan).
- Tabel `listing` punya kolom tambahan `nama_makanan` (keputusan 12 Nov, karena mockup menampilkannya).

Urutan ketergantungan: titik_pengambilan (PBI-03) lalu listing lalu permintaan lalu jadwal_pickup lalu serah terima, ulasan, laporan dampak. Notifikasi (PBI-21) dipakai modul 3 dan 4.

## Istilah dan status baku (pakai persis ini di kode dan layar)
- **Listing**: unggahan makanan oleh Donatur (Mitra terverifikasi). **Postingan sharing**: unggahan individu di forum (tanpa persetujuan). **Permintaan**: pengajuan Penerima atas listing, perlu disetujui Donatur. **Klaim**: ambil porsi di postingan sharing. **Pickup**: pengambilan di slot waktu dan titik yang dijadwalkan. **Antar**: pengantaran oleh Relawan. **Kode pickup**: kode unik Penerima untuk dikonfirmasi Donatur. **Serah terima**: konfirmasi kode. **Verifikasi**: pemeriksaan mitra oleh Admin (bukan penerima). **Dampak**: porsi terselamatkan + estimasi berat.
- Status Mitra: Menunggu verifikasi, Terverifikasi, Ditolak, Ditangguhkan.
- Status Listing: Aktif, Ditutup (oleh Donatur), Habis (stok 0), Kedaluwarsa (oleh Sistem), Ditangguhkan (oleh Admin).
- Status Permintaan: Menunggu, Disetujui, Ditolak, Dibatalkan (oleh Penerima), Selesai (setelah serah terima).
- Status Pickup/Antar: Dijadwalkan, Dalam pengantaran (jika diantar), Diserahkan, Selesai, Dibatalkan.
- Status Postingan sharing: Aktif, Habis, Ditutup, Kedaluwarsa, Ditangguhkan.
- Status Laporan masalah: Baru, Ditinjau, Ditindaklanjuti, Ditolak.
- Tipe listing: Gratis atau Diskon (ada harga). Kategori makanan: Makanan berat, Roti & kue, Buah & sayur, Minuman, Lainnya.
- Status akun: Aktif, Ditangguhkan (tidak boleh login).
Kalau proposal Bab 4.3 menyebut nilai berbeda untuk sebuah kolom (mis. status klaim "Menunggu Persetujuan", status artikel Draf/Terbit/Diarsipkan), ikuti proposal.

## Aturan penulisan ID
PBI-NN, FR-NN, UC-NN, SCR-NN.x seperti di proposal. Jangan membuat atau mengubah ID. Jangan mengarang fitur, tabel, atau kolom; kalau tidak jelas, tanya Nug.
