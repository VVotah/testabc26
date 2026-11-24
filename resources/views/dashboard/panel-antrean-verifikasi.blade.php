{{-- Isi panel "Antrean verifikasi mitra" di Dashboard Admin (mockup 4.5.5). --}}
<ul class="fl-daftar">
    @foreach ($antrean as $item)
        <li>
            <span class="fl-daftar-gambar fl-daftar-ikon"><x-ikon nama="toko" /></span>
            <div class="fl-daftar-isi">
                <div class="fl-daftar-judul">{{ $item->mitra->nama_mitra }}</div>
                <div class="fl-daftar-ket">{{ $item->mitra->jenis_mitra }} &middot; diajukan {{ \App\Support\Waktu::relatif($item->tanggal_pengajuan) }}</div>
            </div>
            <div class="fl-daftar-aksi">
                <a href="{{ route('verifikasi-mitra.show', $item) }}" class="btn btn-utama btn-sm">Tinjau</a>
            </div>
        </li>
    @endforeach
</ul>
