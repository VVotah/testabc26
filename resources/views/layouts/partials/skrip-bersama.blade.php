{{-- Skrip kecil untuk komponen bersama. Dimuat oleh layouts.app dan layouts.tamu. --}}
<script>
    // Tombol Lihat di kolom kata sandi: <button class="fl-lihat-sandi" aria-controls="id-kolom">.
    document.querySelectorAll('.fl-lihat-sandi').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            const kolom = document.getElementById(tombol.getAttribute('aria-controls'));
            const tampil = kolom.type === 'password';
            kolom.type = tampil ? 'text' : 'password';
            tombol.textContent = tampil ? 'Tutup' : 'Lihat';
            tombol.setAttribute('aria-pressed', tampil);
        });
    });

    // Tombol kirim dengan data-memproses="Teks": cegah kirim ganda dan tampilkan status memproses.
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const tombol = form.querySelector('button[type="submit"][data-memproses]');
            if (!tombol) return;
            tombol.classList.add('memproses');
            tombol.textContent = tombol.dataset.memproses;
        });
    });

    // Kotak unggah berkas (.fl-unggah): tampilkan nama berkas dan sorot saat berkas diseret.
    document.querySelectorAll('.fl-unggah').forEach(function (kotak) {
        const input = kotak.querySelector('input[type="file"]');
        if (!input) return;

        const tampilkan = function () {
            if (input.files.length) {
                kotak.setAttribute('data-berkas', Array.from(input.files).map(function (f) { return f.name; }).join(', '));
            } else {
                kotak.removeAttribute('data-berkas');
            }
        };

        input.addEventListener('change', tampilkan);
        ['dragenter', 'dragover'].forEach(function (nama) {
            kotak.addEventListener(nama, function () { kotak.classList.add('diseret'); });
        });
        ['dragleave', 'drop'].forEach(function (nama) {
            kotak.addEventListener(nama, function () { kotak.classList.remove('diseret'); });
        });
        tampilkan();
    });
</script>
