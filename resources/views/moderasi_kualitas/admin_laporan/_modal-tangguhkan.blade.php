{{--
    Konfirmasi sebelum menangguhkan listing atau akun (proposal 4.5.51).
    Muncul saat Admin memilih "Tangguhkan" lalu menekan Simpan. Tombol "Ya, tangguhkan"
    mengisi konfirmasi_penangguhan=1; server menolak penangguhan tanpa konfirmasi ini.
--}}
<div class="modal fade" id="modalTangguhkan" tabindex="-1" aria-labelledby="judulTangguhkan" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h2 class="modal-title" id="judulTangguhkan">Tangguhkan {{ strtolower($laporan->jenis_objek) }} ini?</h2>
                <p class="text-muted mt-2 mb-4">
                    {{ $laporan->jenis_objek }} <strong class="text-body">"{{ $laporan->nama_objek }}"</strong> akan ditangguhkan.
                    @if ($laporan->jenis_objek === \App\Models\LaporanMasalah::OBJEK_AKUN)
                        Pemilik akun langsung dikeluarkan dan tidak bisa masuk sampai akunnya diaktifkan lagi.
                    @else
                        Listing tidak tampil lagi di katalog penerima.
                    @endif
                </p>
                <div class="fl-form-aksi mt-0">
                    <button type="button" class="btn btn-sekunder" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-bahaya" id="tombolYakinTangguhkan">Ya, tangguhkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('formTindakLanjut');
            const konfirmasi = document.getElementById('konfirmasiPenangguhan');
            const modal = new bootstrap.Modal(document.getElementById('modalTangguhkan'));

            // Dipasang di fase capture supaya jalan sebelum skrip data-memproses bersama.
            form.addEventListener('submit', function (e) {
                const tangguhkan = form.querySelector('input[name="aksi_penangguhan"]:checked')?.value === 'tangguhkan';
                // Status Ditolak tidak boleh menangguhkan; biarkan server yang menolak dengan pesannya.
                const ditolak = form.querySelector('#status_laporan').value === 'Ditolak';
                if (tangguhkan && !ditolak && konfirmasi.value !== '1') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    modal.show();
                }
            }, true);

            document.getElementById('tombolYakinTangguhkan').addEventListener('click', function () {
                konfirmasi.value = '1';
                modal.hide();
                form.requestSubmit();
            });

            // Pilihan berubah: konfirmasi lama tidak berlaku lagi.
            form.querySelectorAll('input[name="aksi_penangguhan"]').forEach(function (r) {
                r.addEventListener('change', function () { konfirmasi.value = '0'; });
            });
        })();
    </script>
@endpush
