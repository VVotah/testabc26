{{-- Kartu "Keputusan admin" (mockup SCR-00.7). Setelah diputuskan, berganti ringkasan hasil. --}}
@if ($pengajuan->sudahDiputuskan())
    <x-panel judul="Keputusan admin">
        <div class="d-flex align-items-center gap-2 mb-2">
            <x-status :nilai="$pengajuan->keputusan" />
            <span class="text-muted small">oleh {{ $pengajuan->admin?->nama ?? 'Admin' }},
                <span class="fl-data">{{ \App\Support\Waktu::lengkap($pengajuan->tanggal_keputusan) }}</span></span>
        </div>
        @if ($pengajuan->alasan)
            <p class="mb-0"><span class="fw-bold">Alasan:</span> {{ $pengajuan->alasan }}</p>
        @else
            <p class="mb-0 text-muted">Mitra ini dapat membuat listing.</p>
        @endif
    </x-panel>
@else
    @php $pilihan = old('keputusan', 'setujui'); @endphp
    <x-panel judul="Keputusan admin">
        <form method="POST" action="{{ route('verifikasi-mitra.putuskan', $pengajuan) }}" id="formKeputusan" novalidate>
            @csrf

            <fieldset>
                <legend class="visually-hidden">Keputusan</legend>
                <div class="fl-pilihan fl-pilihan-tumpuk @error('keputusan') is-invalid @enderror">
                    <label class="fl-pilihan-kartu">
                        <input type="radio" name="keputusan" value="setujui" @checked($pilihan === 'setujui')>
                        <span class="fl-pilihan-ikon"><x-ikon nama="centang" /></span>
                        <span class="fl-pilihan-judul">Setujui</span>
                        <span class="fl-pilihan-ket">Mitra dapat membuat listing.</span>
                    </label>
                    <label class="fl-pilihan-kartu fl-pilihan-bahaya">
                        <input type="radio" name="keputusan" value="tolak" @checked($pilihan === 'tolak')>
                        <span class="fl-pilihan-ikon"><x-ikon nama="bendera" /></span>
                        <span class="fl-pilihan-judul">Tolak</span>
                        <span class="fl-pilihan-ket">Mitra diminta memperbaiki data.</span>
                    </label>
                </div>
                @error('keputusan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </fieldset>

            <div class="mt-3" id="kolomAlasan">
                <label for="alasan" class="form-label">Alasan penolakan</label>
                <textarea id="alasan" name="alasan" rows="3" maxlength="255"
                    class="form-control @error('alasan') is-invalid @enderror"
                    placeholder="Mis. Dokumen NIB tidak terbaca, mohon unggah ulang."
                    aria-describedby="bantuanAlasan @error('alasan') galatAlasan @enderror">{{ old('alasan') }}</textarea>
                <div class="form-text" id="bantuanAlasan">Wajib diisi jika ditolak. Alasan ini tampil di dashboard mitra.</div>
                @error('alasan')
                    <div class="invalid-feedback" id="galatAlasan">{{ $message }}</div>
                @enderror
            </div>

            <div class="fl-form-aksi">
                <button type="submit" id="tombolKeputusan"
                    class="btn {{ $pilihan === 'tolak' ? 'btn-bahaya' : 'btn-utama' }}"
                    data-memproses="Menyimpan keputusan">{{ $pilihan === 'tolak' ? 'Tolak pengajuan' : 'Setujui mitra' }}</button>
            </div>
        </form>
    </x-panel>

    @push('scripts')
        <script>
            // Label dan warna tombol mengikuti pilihan; alasan wajib hanya saat menolak.
            (function () {
                const form = document.getElementById('formKeputusan');
                const tombol = document.getElementById('tombolKeputusan');
                const alasan = document.getElementById('alasan');
                const atur = function () {
                    const tolak = form.querySelector('input[name="keputusan"]:checked')?.value === 'tolak';
                    tombol.textContent = tolak ? 'Tolak pengajuan' : 'Setujui mitra';
                    tombol.classList.toggle('btn-bahaya', tolak);
                    tombol.classList.toggle('btn-utama', !tolak);
                    alasan.required = tolak;
                };
                form.querySelectorAll('input[name="keputusan"]').forEach(function (r) { r.addEventListener('change', atur); });
                atur();
            })();
        </script>
    @endpush
@endif
