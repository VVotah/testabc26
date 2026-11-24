<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\VerifikasiMitra;
use Database\Seeders\PeranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DaftarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PeranSeeder::class);
        Storage::fake('local');
    }

    private function dataPenerima(array $ubah = []): array
    {
        return array_merge([
            'nama' => 'Rina Penerima',
            'email' => 'rina@contoh.test',
            'kata_sandi' => 'rahasia123',
            'peran' => 'penerima',
            'setuju' => '1',
        ], $ubah);
    }

    private function dataMitra(array $ubah = []): array
    {
        return array_merge([
            'nama_mitra' => 'Warung Bu Sari',
            'jenis_mitra' => 'Restoran',
            'nama' => 'Sari Wulandari',
            'alamat_jalan' => 'Jl. Dipatiukur No. 12, Coblong',
            'kota' => 'Bandung',
            'kode_pos' => '40132',
            'email' => 'sari@contoh.test',
            'kata_sandi' => 'rahasia123',
            'bukti_pendukung' => UploadedFile::fake()->create('nib.pdf', 300, 'application/pdf'),
            'setuju' => '1',
        ], $ubah);
    }

    // ---------- Penerima / Relawan ----------

    public function test_halaman_daftar_tampil_untuk_tamu(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('Buat akun');
        $this->get(route('register.mitra'))->assertOk()->assertSee('Daftar sebagai mitra');
    }

    public function test_daftar_penerima_berhasil_dan_langsung_masuk(): void
    {
        $this->post(route('register.simpan'), $this->dataPenerima())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('sukses');

        $pengguna = Pengguna::where('email', 'rina@contoh.test')->first();
        $this->assertNotNull($pengguna);
        $this->assertTrue($pengguna->punyaPeran(Peran::PENERIMA));
        $this->assertSame(Pengguna::STATUS_AKTIF, $pengguna->status_akun);
        $this->assertAuthenticatedAs($pengguna);
    }

    public function test_daftar_relawan_berhasil(): void
    {
        $this->post(route('register.simpan'), $this->dataPenerima(['email' => 'budi@contoh.test', 'peran' => 'relawan']));

        $this->assertTrue(Pengguna::where('email', 'budi@contoh.test')->first()->punyaPeran(Peran::RELAWAN));
    }

    public function test_kata_sandi_disimpan_sebagai_hash(): void
    {
        $this->post(route('register.simpan'), $this->dataPenerima());

        $sandi = Pengguna::where('email', 'rina@contoh.test')->value('kata_sandi');
        $this->assertNotSame('rahasia123', $sandi);
        $this->assertTrue(Hash::check('rahasia123', $sandi));
    }

    public function test_peran_admin_dan_mitra_tidak_bisa_dipilih_dari_form(): void
    {
        foreach (['admin', 'Admin', 'mitra', 'Mitra/Donatur', '1'] as $peranPalsu) {
            $this->post(route('register.simpan'), $this->dataPenerima(['peran' => $peranPalsu]))
                ->assertSessionHasErrors('peran');
        }

        $this->assertDatabaseCount('pengguna', 0);
    }

    public function test_id_peran_dari_form_diabaikan(): void
    {
        $idAdmin = Peran::where('nama_peran', Peran::ADMIN)->value('id_peran');

        $this->post(route('register.simpan'), $this->dataPenerima(['id_peran' => $idAdmin]));

        $pengguna = Pengguna::where('email', 'rina@contoh.test')->first();
        $this->assertTrue($pengguna->punyaPeran(Peran::PENERIMA));
        $this->assertFalse($pengguna->punyaPeran(Peran::ADMIN));
    }

    public function test_email_kembar_ditolak(): void
    {
        $this->post(route('register.simpan'), $this->dataPenerima());
        $this->post(route('logout'));

        $this->post(route('register.simpan'), $this->dataPenerima(['nama' => 'Orang Lain']))
            ->assertSessionHasErrors(['email' => 'Email ini sudah terdaftar. Silakan masuk.']);

        $this->assertDatabaseCount('pengguna', 1);
    }

    public function test_field_wajib_dan_persetujuan(): void
    {
        $this->post(route('register.simpan'), [])
            ->assertSessionHasErrors(['nama', 'email', 'kata_sandi', 'peran', 'setuju']);

        $this->post(route('register.simpan'), $this->dataPenerima(['kata_sandi' => 'pendek']))
            ->assertSessionHasErrors('kata_sandi');

        $this->post(route('register.simpan'), $this->dataPenerima(['setuju' => null]))
            ->assertSessionHasErrors('setuju');

        $this->assertGuest();
    }

    public function test_pengguna_yang_sudah_masuk_diarahkan_ke_dashboard(): void
    {
        $this->post(route('register.simpan'), $this->dataPenerima());

        $this->get(route('register'))->assertRedirect(route('dashboard'));
        $this->get(route('register.mitra'))->assertRedirect(route('dashboard'));
    }

    // ---------- Mitra ----------

    public function test_daftar_mitra_mengisi_tiga_tabel_dan_menyimpan_dokumen_privat(): void
    {
        $this->post(route('register.mitra.simpan'), $this->dataMitra())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('sukses');

        $pengguna = Pengguna::where('email', 'sari@contoh.test')->first();
        $this->assertTrue($pengguna->punyaPeran(Peran::MITRA));
        $this->assertTrue(Hash::check('rahasia123', $pengguna->kata_sandi));
        $this->assertAuthenticatedAs($pengguna);

        $mitra = $pengguna->mitra;
        $this->assertSame('Warung Bu Sari', $mitra->nama_mitra);
        $this->assertSame('Restoran', $mitra->jenis_mitra);
        $this->assertSame(Mitra::STATUS_MENUNGGU, $mitra->status_mitra);
        $this->assertSame('Jl. Dipatiukur No. 12, Coblong, Bandung, 40132', $mitra->alamat);
        $this->assertSame('Bandung', $mitra->kota);
        $this->assertSame('40132', $mitra->kode_pos);

        $verifikasi = VerifikasiMitra::where('id_mitra', $mitra->id_mitra)->first();
        $this->assertNotNull($verifikasi->tanggal_pengajuan);
        $this->assertNull($verifikasi->keputusan);
        $this->assertNull($verifikasi->id_admin);
        Storage::disk('local')->assertExists($verifikasi->bukti_pendukung);
    }

    public function test_daftar_mitra_mengabaikan_peran_dan_status_dari_form(): void
    {
        $idAdmin = Peran::where('nama_peran', Peran::ADMIN)->value('id_peran');

        $this->post(route('register.mitra.simpan'), $this->dataMitra([
            'id_peran' => $idAdmin,
            'status_mitra' => Mitra::STATUS_TERVERIFIKASI,
        ]));

        $pengguna = Pengguna::where('email', 'sari@contoh.test')->first();
        $this->assertTrue($pengguna->punyaPeran(Peran::MITRA));
        $this->assertSame(Mitra::STATUS_MENUNGGU, $pengguna->mitra->status_mitra);
    }

    public function test_dashboard_mitra_baru_menampilkan_menunggu_verifikasi(): void
    {
        $this->post(route('register.mitra.simpan'), $this->dataMitra());

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Menunggu verifikasi')
            ->assertSee('sedang ditinjau Admin');
    }

    public function test_jenis_mitra_harus_sesuai_erd(): void
    {
        $this->post(route('register.mitra.simpan'), $this->dataMitra(['jenis_mitra' => 'Kafe']))
            ->assertSessionHasErrors('jenis_mitra');

        $this->assertDatabaseCount('mitra', 0);
    }

    public function test_dokumen_salah_tipe_atau_terlalu_besar_ditolak(): void
    {
        $this->post(route('register.mitra.simpan'), $this->dataMitra([
            'bukti_pendukung' => UploadedFile::fake()->create('nib.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]))->assertSessionHasErrors(['bukti_pendukung' => 'Dokumen harus berformat PDF, JPG, atau PNG.']);

        $this->post(route('register.mitra.simpan'), $this->dataMitra([
            'bukti_pendukung' => UploadedFile::fake()->create('nib.pdf', 5121, 'application/pdf'),
        ]))->assertSessionHasErrors(['bukti_pendukung' => 'Ukuran dokumen maksimal 5 MB.']);

        $this->post(route('register.mitra.simpan'), $this->dataMitra(['bukti_pendukung' => null]))
            ->assertSessionHasErrors('bukti_pendukung');

        $this->assertDatabaseCount('pengguna', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_kode_pos_dan_kota_divalidasi(): void
    {
        foreach (['4013', '401321', 'abcde'] as $kodePos) {
            $this->post(route('register.mitra.simpan'), $this->dataMitra(['kode_pos' => $kodePos]))
                ->assertSessionHasErrors('kode_pos');
        }

        $this->post(route('register.mitra.simpan'), $this->dataMitra(['kota' => 'Bandung, Jawa Barat']))
            ->assertSessionHasErrors('kota');

        $this->assertDatabaseCount('mitra', 0);
    }

    public function test_field_wajib_mitra(): void
    {
        $this->post(route('register.mitra.simpan'), [])
            ->assertSessionHasErrors([
                'nama_mitra', 'jenis_mitra', 'nama', 'alamat_jalan', 'kota', 'kode_pos',
                'email', 'kata_sandi', 'bukti_pendukung', 'setuju',
            ]);
    }
}
