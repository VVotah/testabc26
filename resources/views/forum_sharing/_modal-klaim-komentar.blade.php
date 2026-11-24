{{--
    Konfirmasi batalkan klaim dan hapus komentar (PBI-14), mengikuti pola
    resources/views/akun_mitra/titik/_modal-hapus.blade.php.
    Tombol pemicu memberi data-aksi (URL) dan data-nama; satu modal dipakai untuk semua baris.
--}}
<div class="modal fade" id="modalBatalKlaim" tabindex="-1" aria-labelledby="judulBatalKlaim" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h2 class="modal-title" id="judulBatalKlaim">Batalkan klaim ini?</h2>
                <p class="text-muted mt-2 mb-4">
                    Klaim <strong class="text-body" id="namaKlaimBatal">ini</strong> akan dibatalkan dan tidak
                    bisa diaktifkan lagi. Setelah dibatalkan, kamu tidak bisa berkomentar di postingan ini
                    kecuali mengajukan klaim baru.
                </p>
                <form method="POST" action="" id="formBatalKlaim" class="fl-form-aksi mt-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sekunder" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bahaya" data-memproses="Membatalkan">Ya, batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusKomentar" tabindex="-1" aria-labelledby="judulHapusKomentar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h2 class="modal-title" id="judulHapusKomentar">Hapus komentar ini?</h2>
                <p class="text-muted mt-2 mb-4">Komentar akan dihapus dari postingan dan tidak dapat dikembalikan.</p>
                <form method="POST" action="" id="formHapusKomentar" class="fl-form-aksi mt-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sekunder" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bahaya" data-memproses="Menghapus">Ya, hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('modalBatalKlaim').addEventListener('show.bs.modal', function (e) {
            const pemicu = e.relatedTarget;
            document.getElementById('formBatalKlaim').action = pemicu.dataset.aksi;
            document.getElementById('namaKlaimBatal').textContent = '"' + pemicu.dataset.nama + '"';
        });

        document.getElementById('modalHapusKomentar').addEventListener('show.bs.modal', function (e) {
            document.getElementById('formHapusKomentar').action = e.relatedTarget.dataset.aksi;
        });
    </script>
@endpush
