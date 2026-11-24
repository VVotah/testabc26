{{--
    Konfirmasi hapus titik. Tombol pemicu memberi data-nama dan data-aksi (URL hapus);
    satu modal dipakai untuk semua baris.
--}}
<div class="modal fade" id="modalHapusTitik" tabindex="-1" aria-labelledby="judulHapusTitik" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h2 class="modal-title" id="judulHapusTitik">Hapus titik ini?</h2>
                <p class="text-muted mt-2 mb-4">
                    Titik <strong class="text-body" id="namaTitikHapus">ini</strong> akan dihapus dari daftar.
                    Titik yang masih dipakai listing aktif tidak bisa dihapus.
                </p>
                <form method="POST" action="" id="formHapusTitik" class="fl-form-aksi mt-0">
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
        document.getElementById('modalHapusTitik').addEventListener('show.bs.modal', function (e) {
            const pemicu = e.relatedTarget;
            document.getElementById('formHapusTitik').action = pemicu.dataset.aksi;
            document.getElementById('namaTitikHapus').textContent = '"' + pemicu.dataset.nama + '"';
        });
    </script>
@endpush
