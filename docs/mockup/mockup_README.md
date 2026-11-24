# Mockup FoodLoop (Figma)

File: https://www.figma.com/design/GKIIdmZGblx9he844xJCYF/
Cara buka frame: tambahkan `?node-id=<ID dengan tanda - bukan :>`, contoh frame Login = `?node-id=132-2`.

Mockup = wireframe abu-abu. Tiru struktur dan isi, bukan menambah dekorasi.

## Frame yang sudah ada
| Halaman | Node ID | Dipakai untuk |
|---|---|---|
| Login | 132:2 | Fondasi (Nug) |
| Dashboard Mitra + menu profil terbuka | 133:2 | Layout dasar (sidebar, topbar, kartu statistik, 2 panel) |
| Dashboard Admin | 134:115 | Fondasi / Admin |
| Daftar sebagai mitra | 135:2 | Fondasi (FOOD-31) |
| Tinjau pengajuan mitra (Admin) | 136:2 | Fondasi (FOOD-33) |
| Profil saya | 266:6 | PBI-01 |
| Profil Mitra | 266:112 | PBI-02 |
| Titik Pengambilan (daftar) | 267:2 | PBI-03 |
| Tambah titik pengambilan | 267:140 | PBI-03 |

Modul lain belum punya frame di Figma: pakai pola layout yang sama (sidebar + topbar + kartu putih).

## Cara melihat mockup untuk kamu
1. Buka link Figma di atas, klik frame yang kamu kerjakan.
2. Export PNG: pilih frame, panel kanan "Export", klik Export. Simpan di `docs/mockup/<modul>/nama-halaman.png` lalu commit.
3. Atau, kalau Figma MCP sudah tersambung di Claude Code, kasih link frame ke Claude Code.

## Menu sidebar per peran (dari mockup)
- Admin: Dashboard, Verifikasi Mitra, Laporan Masuk, Artikel Edukasi, Kampanye & Event, Pengguna
- Mitra: Dashboard, Jadwal Surplus, Listing Makanan, Permintaan Masuk, Pickup, Titik Pengambilan, Laporan Dampak, Profil Mitra
