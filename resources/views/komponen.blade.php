{{-- Katalog komponen tampilan. Hanya tersedia di APP_ENV=local (lihat routes/web.php). --}}
@extends('layouts.app')

@section('title', 'Komponen tampilan')

@section('content')
    <x-kepala-halaman judul="Komponen tampilan"
        keterangan="Semua yang tampil di sini siap dipakai di view modul. Salin kodenya, jangan menulis warna sendiri.">
        <button type="button" class="btn btn-sekunder">Aksi sekunder</button>
        <button type="button" class="btn btn-utama"><x-ikon nama="tambah" />Aksi utama</button>
    </x-kepala-halaman>

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <x-panel judul="Tombol" class="h-100">
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="button" class="btn btn-utama">Simpan perubahan</button>
                    <button type="button" class="btn btn-sekunder">Batal</button>
                    <button type="button" class="btn btn-aksen">Ajukan permintaan</button>
                    <button type="button" class="btn btn-bahaya">Tangguhkan</button>
                    <button type="button" class="btn btn-outline-danger">Hapus</button>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="button" class="btn btn-sekunder btn-sm">Ubah</button>
                    <button type="button" class="btn btn-outline-danger btn-sm">Hapus</button>
                    <button type="button" class="btn btn-utama btn-sm">Setujui</button>
                    <button type="button" class="btn btn-utama" disabled>Nonaktif</button>
                </div>
                <p class="form-text mt-3 mb-0">
                    <code>btn-utama</code> satu per layar. <code>btn-aksen</code> (oranye) hanya untuk ajakan terpenting.
                    <code>btn-primary</code> dan <code>btn-dark</code> otomatis tampil seperti <code>btn-utama</code>.
                </p>
            </x-panel>
        </div>

        <div class="col-12 col-xl-6">
            <x-panel judul="Badge status" class="h-100">
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach (['Aktif', 'Terverifikasi', 'Disetujui', 'Selesai', 'Menunggu', 'Menunggu verifikasi', 'Dijadwalkan', 'Draf', 'Dalam pengantaran', 'Ditolak', 'Ditangguhkan', 'Ditutup', 'Habis', 'Kedaluwarsa', 'Dibatalkan'] as $contohStatus)
                        <x-status :nilai="$contohStatus" />
                    @endforeach
                </div>
                <p class="form-text mt-3 mb-0">
                    <code>&lt;x-status :nilai="$listing-&gt;status_listing" /&gt;</code>. Warnanya dipilih otomatis dari teks status baku.
                </p>
            </x-panel>
        </div>

        <div class="col-12">
            <div class="fl-statistik-baris">
                <x-statistik label="Listing aktif" :angka="8" ikon="kotak" />
                <x-statistik label="Permintaan baru" :angka="5" ikon="masuk" catatan="2 menunggu lebih dari sehari" />
                <x-statistik label="Porsi terselamatkan" :angka="1240" ikon="daun" />
                <x-statistik label="Data belum tersedia" ikon="jam" />
            </div>
            <p class="form-text mt-2 mb-0">
                <code>&lt;div class="fl-statistik-baris"&gt;</code> berisi beberapa
                <code>&lt;x-statistik label="..." :angka="$n" ikon="kotak" /&gt;</code>. Angka <code>null</code> (modul belum dibuat) tampil sebagai Segera hadir.
            </p>
        </div>

        <div class="col-12 col-xl-6">
            <x-panel judul="Daftar baris" class="h-100">
                <x-slot:aksi><button type="button" class="btn btn-sekunder btn-sm"><x-ikon nama="tambah" />Tambah titik</button></x-slot:aksi>
                <ul class="fl-daftar">
                    <li>
                        <div class="fl-daftar-gambar"></div>
                        <div class="fl-daftar-isi">
                            <div class="fl-daftar-judul">Warung Utama</div>
                            <div class="fl-daftar-ket">Jl. Dipatiukur No. 12, Coblong, Bandung</div>
                        </div>
                        <div class="fl-daftar-aksi">
                            <button type="button" class="btn btn-sekunder btn-sm">Ubah</button>
                            <button type="button" class="btn btn-outline-danger btn-sm">Hapus</button>
                        </div>
                    </li>
                    <li>
                        <div class="fl-daftar-gambar"></div>
                        <div class="fl-daftar-isi">
                            <div class="fl-daftar-judul">Panti Asuhan Kasih</div>
                            <div class="fl-daftar-ket">20 porsi, nasi kotak</div>
                        </div>
                        <div class="fl-daftar-aksi"><x-status nilai="Menunggu" /></div>
                    </li>
                </ul>
            </x-panel>
        </div>

        <div class="col-12 col-xl-6">
            <x-panel judul="Tampilan kosong" class="h-100">
                <x-kosong judul="Belum ada titik pengambilan" ikon="lokasi">
                    Tambahkan titik supaya bisa dipilih saat membuat listing.
                    <x-slot:aksi><button type="button" class="btn btn-utama btn-sm"><x-ikon nama="tambah" />Tambah titik</button></x-slot:aksi>
                </x-kosong>
            </x-panel>
        </div>

        <div class="col-12">
            <div class="fl-tabel">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Makanan</th>
                            <th>Titik pengambilan</th>
                            <th class="text-end">Porsi</th>
                            <th>Kedaluwarsa</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Roti & pastry</td>
                            <td>Warung Utama</td>
                            <td class="text-end">24</td>
                            <td class="fl-data">10 Nov 2026, 19.00</td>
                            <td><x-status nilai="Aktif" /></td>
                            <td class="text-end"><button type="button" class="btn btn-sekunder btn-sm">Ubah</button></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nasi kotak</td>
                            <td>Dapur Produksi</td>
                            <td class="text-end">0</td>
                            <td class="fl-data">9 Nov 2026, 21.00</td>
                            <td><x-status nilai="Habis" /></td>
                            <td class="text-end"><button type="button" class="btn btn-sekunder btn-sm">Ubah</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="form-text mt-2 mb-0">Bungkus <code>&lt;table class="table"&gt;</code> dengan <code>&lt;div class="fl-tabel"&gt;</code>.</p>
        </div>

        <div class="col-12 col-xl-7">
            <x-panel judul="Form">
                <form class="mt-3" onsubmit="return false">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contohNama" class="form-label">Nama titik</label>
                            <input type="text" id="contohNama" class="form-control" placeholder="Mis. Dapur utama">
                        </div>
                        <div class="col-md-6">
                            <label for="contohJenis" class="form-label">Jenis mitra</label>
                            <select id="contohJenis" class="form-select">
                                <option>Restoran</option>
                                <option>Toko roti</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="contohPatokan" class="form-label">Patokan <span class="fl-opsional">opsional</span></label>
                            <input type="text" id="contohPatokan" class="form-control" placeholder="Mis. Pintu belakang, dekat parkiran">
                        </div>
                        <div class="col-12">
                            <fieldset class="fl-koordinat mt-0">
                                <div class="fl-koordinat-kepala">
                                    <legend class="form-label mb-0">Koordinat</legend>
                                    <button type="button" class="btn btn-sekunder btn-sm"><x-ikon nama="lokasi" />Gunakan lokasi saya</button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label for="contohLat" class="form-label fl-label-kecil">Latitude</label>
                                        <input type="text" id="contohLat" class="form-control fl-data" placeholder="Mis. -6.8915" value="-6.8915" inputmode="decimal">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="contohLng" class="form-label fl-label-kecil">Longitude</label>
                                        <input type="text" id="contohLng" class="form-control fl-data" placeholder="Mis. 107.6107" inputmode="decimal">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-12">
                            <label for="contohSalah" class="form-label">Latitude</label>
                            <input type="text" id="contohSalah" class="form-control is-invalid" value="abc" aria-describedby="contohGalat">
                            <div class="invalid-feedback" id="contohGalat">Latitude harus berupa angka antara -90 dan 90.</div>
                        </div>
                        <div class="col-12">
                            <span class="form-label d-block">Pilihan berupa kartu</span>
                            <div class="fl-pilihan" role="radiogroup" aria-label="Contoh pilihan peran">
                                <label class="fl-pilihan-kartu">
                                    <input type="radio" name="contohPeran" value="penerima" checked>
                                    <span class="fl-pilihan-ikon"><x-ikon nama="hati" /></span>
                                    <span class="fl-pilihan-judul">Penerima</span>
                                    <span class="fl-pilihan-ket">Mencari dan meminta makanan.</span>
                                </label>
                                <label class="fl-pilihan-kartu">
                                    <input type="radio" name="contohPeran" value="relawan">
                                    <span class="fl-pilihan-ikon"><x-ikon nama="sepeda" /></span>
                                    <span class="fl-pilihan-judul">Relawan</span>
                                    <span class="fl-pilihan-ket">Membantu mengantar makanan.</span>
                                </label>
                            </div>
                            <div class="form-text"><code>.fl-pilihan</code> berisi <code>label.fl-pilihan-kartu</code> dengan <code>input type="radio"</code> di dalamnya.</div>
                        </div>
                        <div class="col-12">
                            <span class="form-label d-block">Unggah berkas</span>
                            <label class="fl-unggah">
                                <input type="file" accept=".pdf,.jpg,.jpeg,.png">
                                <span class="fl-unggah-ikon"><x-ikon nama="unggah" /></span>
                                <span class="fl-unggah-judul">Seret berkas ke sini atau <u>pilih berkas</u></span>
                                <span class="fl-unggah-ket">PDF, JPG, atau PNG, maks. 5 MB</span>
                            </label>
                            <div class="form-text"><code>label.fl-unggah</code> berisi <code>input type="file"</code>; nama berkas tampil otomatis setelah dipilih.</div>
                        </div>
                        <div class="col-12">
                            <label for="contohKunci" class="form-label">Peran</label>
                            <input type="text" id="contohKunci" class="form-control" value="Mitra/Donatur" disabled>
                            <div class="form-text">Peran tidak dapat diubah.</div>
                        </div>
                    </div>
                    <div class="fl-form-aksi">
                        <button type="button" class="btn btn-sekunder">Batal</button>
                        <button type="submit" class="btn btn-utama">Simpan titik</button>
                    </div>
                </form>
            </x-panel>
        </div>

        <div class="col-12 col-xl-5">
            <x-panel judul="Pesan">
                <div class="d-grid gap-2 mt-3">
                    <div class="alert alert-success mb-0" role="alert">Titik pengambilan berhasil disimpan.</div>
                    <div class="alert alert-danger mb-0" role="alert">Titik masih dipakai listing berstatus Aktif.</div>
                    <div class="alert alert-warning mb-0" role="alert">Akun mitra kamu masih menunggu verifikasi.</div>
                </div>
                <p class="form-text mt-3 mb-0">
                    <code>@@include('partials.pesan')</code> di view, lalu dari controller
                    <code>-&gt;with('sukses', '...')</code> atau <code>-&gt;with('galat', '...')</code>.
                </p>
            </x-panel>
        </div>
    </div>
@endsection
