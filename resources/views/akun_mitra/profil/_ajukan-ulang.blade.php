{{--
    Panel pengajuan verifikasi ulang, hanya untuk mitra berstatus Ditolak (opsi B, disetujui Nug):
    mitra memperbaiki data di form Profil Mitra, lalu mengajukan ulang di sini.
--}}
<x-panel judul="Pengajuan ditolak" class="fl-kartu-isi fl-panel-perhatian mb-4">
    <p class="mb-1">
        Admin menolak pengajuanmu
        @if ($mitra->verifikasiTerakhir?->tanggal_keputusan)
            pada {{ \App\Support\Waktu::lengkap($mitra->verifikasiTerakhir->tanggal_keputusan) }}
        @endif
        dengan alasan:
    </p>
    <p class="fl-kutipan">{{ $mitra->verifikasiTerakhir?->alasan ?? 'Tidak ada alasan yang dicatat.' }}</p>

    <ol class="fl-langkah-urut">
        <li>Perbaiki data usaha di form di bawah, lalu tekan <strong>Simpan perubahan</strong>.</li>
        <li>Bila yang bermasalah dokumennya, unggah dokumen baru. Bila tidak, dokumen sebelumnya dipakai lagi.</li>
        <li>Tekan <strong>Ajukan verifikasi ulang</strong>. Admin akan meninjau dalam 1-3 hari kerja.</li>
    </ol>

    <form method="POST" action="{{ route('profil-mitra.ajukan-ulang') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <label class="fl-unggah @error('bukti_pendukung') is-invalid @enderror" for="bukti_pendukung">
            <input type="file" id="bukti_pendukung" name="bukti_pendukung" accept=".pdf,.jpg,.jpeg,.png"
                @error('bukti_pendukung') aria-describedby="galatBukti" aria-invalid="true" @enderror>
            <span class="fl-unggah-ikon"><x-ikon nama="dokumen" /></span>
            <span class="fl-unggah-judul">Seret dokumen baru ke sini atau <u>pilih berkas</u></span>
            <span class="fl-unggah-ket">Opsional. NIB atau KTP penanggung jawab. PDF, JPG, atau PNG, maks. 5 MB.</span>
        </label>
        @error('bukti_pendukung')
            <div class="invalid-feedback" id="galatBukti">{{ $message }}</div>
        @enderror

        <div class="fl-form-aksi">
            <button type="submit" class="btn btn-aksen" data-memproses="Mengirim pengajuan">Ajukan verifikasi ulang</button>
        </div>
    </form>
</x-panel>
