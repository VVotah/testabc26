<div class="modal fade" id="modalKeluar" tabindex="-1" aria-labelledby="judulModalKeluar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h2 class="modal-title" id="judulModalKeluar">Keluar dari aplikasi?</h2>
                <p class="text-muted mt-2 mb-4">Kamu perlu masuk lagi untuk memakai FoodLoop.</p>
                <form method="POST" action="{{ route('logout') }}" class="fl-form-aksi mt-0">
                    @csrf
                    <button type="button" class="btn btn-sekunder" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-utama">Ya, keluar</button>
                </form>
            </div>
        </div>
    </div>
</div>
