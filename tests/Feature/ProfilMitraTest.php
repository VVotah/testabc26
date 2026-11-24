<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\VerifikasiMitra;
use Database\Seeders\PeranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilMitraTest extends TestCase
{
    use RefreshDatabase;

    private Pengguna $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PeranSeeder::class);
        Storage::fake('local');
        $this->admin = $this->buatPengguna(Peran::ADMIN, 'admin@contoh.test');
    }

    private function buatPengguna(string $peran, string $email): Pengguna
    {
        return Pengguna::create([
            'id_peran' => Peran::where('nama_peran', $peran)->value('id_peran'),
            'nama' => 'Uji '.$peran,
            'email' => $email,
            'kata_sandi' => 'rahasia123',
            'status_akun' => Pengguna::STATUS_AKTIF,
        ]);
    }

    /** Mitra beserta satu pengajuan; status Ditolak juga mengisi keputusan dan alasan. */
    private function buatMitra(string $status = Mitra::STATUS_TERVERIFIKASI, string $email = 'sari@contoh.test'): Mitra
    {
        $mitra = Mitra::create([
            'id_pengguna' => $this->buatPengguna(Peran::MITRA, $email)->id_pengguna,
            'nama_mitra' => 'Warung Bu Sari',
            'jenis_mitra' => 'Restoran',
            'alamat' => Mitra::gabungAlamat('Jl. Dipatiukur No. 12, Coblong', 'Bandung', '40132'),
            'status_mitra' => $status,
        ]);

        Storage::disk('local')->put('verifikasi_mitra/lama.pdf', '%PDF-1.4 lama');
        $diputuskan = in_array($status, [Mitra::STATUS_TERVERIFIKASI, Mitra::STATUS_DITOLAK], true);

        VerifikasiMitra::create([
            'id_mitra' => $mitra->id_mitra,
            'bukti_pendukung' => 'verifikasi_mitra/lama.pdf',
            'tanggal_pengajuan' => now()->subDays(3),
            'keputusan' => $diputuskan ? $status : null,
            'alasan' => $status === Mitra::STATUS_DITOLAK ? 'Dokumen NIB tidak terbaca' : null,
            'id_admin' => $diputuskan ? $this->admin->id_pengguna : null,
            'tanggal_keputusan' => $diputuskan ? now()->subDays(2) : null,
        ]);

        return $mitra;
    }

    private function dataProfil(array $ubah = []): array
    {
        return array_merge([
            'nama_mitra' => 'Warung Bu Sari Dago',
            'jenis_mitra' => 'Katering',
            'alamat_jalan' => 'Jl. Ir. H. Juanda No. 45, Dago',
            'kota' => 'Bandung',
            'kode_pos' => '40135',
        ], $ubah);
    }

    // ---------- Akses ----------

    public function test_hanya_mitra_yang_bisa_membuka_profil_mitra(): void
    {
        $this->get(route('profil-mitra.edit'))->assertRedirect(route('login'));
        $this->put(route('profil-mitra.update'), $this->dataProfil())->assertRedirect(route('login'));

        foreach ([Peran::ADMIN, Peran::PENERIMA, Peran::RELAWAN] as $peran) {
            $pengguna = $peran === Peran::ADMIN ? $this->admin : $this->buatPengguna($peran, str()->slug($peran).'@contoh.test');
            $this->actingAs($pengguna)->get(route('profil-mitra.edit'))->assertForbidden();
            $this->actingAs($pengguna)->put(route('profil-mitra.update'), $this->dataProfil())->assertForbidden();
            $this->actingAs($pengguna)->post(route('profil-mitra.ajukan-ulang'))->assertForbidden();
        }
    }

    public function test_semua_status_mitra_bisa_membuka_profil(): void
    {
        foreach ([Mitra::STATUS_MENUNGGU, Mitra::STATUS_TERVERIFIKASI, Mitra::STATUS_DITOLAK, Mitra::STATUS_DITANGGUHKAN] as $i => $status) {
            $mitra = $this->buatMitra($status, "mitra{$i}@contoh.test");
            $this->actingAs($mitra->pengguna)->get(route('profil-mitra.edit'))->assertOk()->assertSee($status);
        }
    }

    // ---------- Lihat ----------

    public function test_profil_terisi_data_sendiri_dengan_alamat_terpecah(): void
    {
        $mitra = $this->buatMitra();

        $this->actingAs($mitra->pengguna)->get(route('profil-mitra.edit'))
            ->assertOk()
            ->assertSee('value="Warung Bu Sari"', false)
            ->assertSee('value="Jl. Dipatiukur No. 12, Coblong"', false)
            ->assertSee('value="Bandung"', false)
            ->assertSee('value="40132"', false)
            ->assertSee('<option value="Restoran" selected', false)
            ->assertSee('href="'.route('dashboard').'"', false); // tombol Batal
    }

    public function test_menu_sidebar_profil_mitra_aktif(): void
    {
        $this->actingAs($this->buatMitra()->pengguna)->get(route('profil-mitra.edit'))
            ->assertSee('href="'.route('profil-mitra.edit').'"', false);
    }

    // ---------- Ubah ----------

    public function test_ubah_profil_menyimpan_dan_menggabung_alamat(): void
    {
        $mitra = $this->buatMitra();

        $this->actingAs($mitra->pengguna)
            ->put(route('profil-mitra.update'), $this->dataProfil())
            ->assertRedirect(route('profil-mitra.edit'))
            ->assertSessionHas('sukses', 'Profil usaha berhasil diperbarui.');

        $mitra->refresh();
        $this->assertSame('Warung Bu Sari Dago', $mitra->nama_mitra);
        $this->assertSame('Katering', $mitra->jenis_mitra);
        $this->assertSame('Jl. Ir. H. Juanda No. 45, Dago, Bandung, 40135', $mitra->alamat);
        $this->assertSame('Jl. Ir. H. Juanda No. 45, Dago', $mitra->alamat_jalan);
        $this->assertSame('40135', $mitra->kode_pos);
    }

    public function test_ubah_profil_tidak_mengubah_status_dan_tidak_memicu_verifikasi_ulang(): void
    {
        foreach ([Mitra::STATUS_TERVERIFIKASI, Mitra::STATUS_MENUNGGU, Mitra::STATUS_DITOLAK] as $i => $status) {
            $mitra = $this->buatMitra($status, "mitra{$i}@contoh.test");

            $this->actingAs($mitra->pengguna)->put(route('profil-mitra.update'), $this->dataProfil());

            $this->assertSame($status, $mitra->fresh()->status_mitra);
            $this->assertSame(1, $mitra->verifikasi()->count());
        }
    }

    public function test_status_dan_id_dari_form_diabaikan(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);
        $lain = $this->buatMitra(Mitra::STATUS_TERVERIFIKASI, 'lain@contoh.test');

        $this->actingAs($mitra->pengguna)->put(route('profil-mitra.update'), $this->dataProfil([
            'status_mitra' => Mitra::STATUS_TERVERIFIKASI,
            'id_mitra' => $lain->id_mitra,
            'id_pengguna' => $lain->id_pengguna,
        ]));

        $this->assertSame(Mitra::STATUS_DITOLAK, $mitra->fresh()->status_mitra);
        $this->assertSame('Warung Bu Sari Dago', $mitra->fresh()->nama_mitra);
        $this->assertSame('Warung Bu Sari', $lain->fresh()->nama_mitra);
    }

    public function test_validasi_dan_isian_dipertahankan(): void
    {
        $mitra = $this->buatMitra();

        $this->actingAs($mitra->pengguna)
            ->from(route('profil-mitra.edit'))
            ->put(route('profil-mitra.update'), $this->dataProfil(['nama_mitra' => '', 'kode_pos' => '401']))
            ->assertRedirect(route('profil-mitra.edit'))
            ->assertSessionHasErrors(['nama_mitra' => 'Nama usaha wajib diisi.', 'kode_pos' => 'Kode pos harus 5 angka.'])
            ->assertSessionHasInput('alamat_jalan', 'Jl. Ir. H. Juanda No. 45, Dago');

        $this->actingAs($mitra->pengguna)->put(route('profil-mitra.update'), $this->dataProfil(['jenis_mitra' => 'Kafe']))
            ->assertSessionHasErrors('jenis_mitra');
        $this->actingAs($mitra->pengguna)->put(route('profil-mitra.update'), $this->dataProfil(['kota' => 'Bandung, Jawa Barat']))
            ->assertSessionHasErrors('kota');
        $this->actingAs($mitra->pengguna)->put(route('profil-mitra.update'), [])
            ->assertSessionHasErrors(['nama_mitra', 'jenis_mitra', 'alamat_jalan', 'kota', 'kode_pos']);

        $this->assertSame('Warung Bu Sari', $mitra->fresh()->nama_mitra);
    }

    // ---------- Pengajuan ulang (opsi B) ----------

    public function test_panel_pengajuan_ulang_hanya_untuk_mitra_ditolak(): void
    {
        $ditolak = $this->buatMitra(Mitra::STATUS_DITOLAK);
        $this->actingAs($ditolak->pengguna)->get(route('profil-mitra.edit'))
            ->assertSee('Pengajuan ditolak')
            ->assertSee('Dokumen NIB tidak terbaca')
            ->assertSee('Ajukan verifikasi ulang');

        $terverifikasi = $this->buatMitra(Mitra::STATUS_TERVERIFIKASI, 'lain@contoh.test');
        $this->actingAs($terverifikasi->pengguna)->get(route('profil-mitra.edit'))
            ->assertDontSee('Ajukan verifikasi ulang');
    }

    public function test_ajukan_ulang_dengan_dokumen_baru(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);

        $this->actingAs($mitra->pengguna)
            ->post(route('profil-mitra.ajukan-ulang'), [
                'bukti_pendukung' => UploadedFile::fake()->create('nib-baru.pdf', 200, 'application/pdf'),
            ])
            ->assertRedirect(route('profil-mitra.edit'))
            ->assertSessionHas('sukses');

        $mitra->refresh();
        $this->assertSame(Mitra::STATUS_MENUNGGU, $mitra->status_mitra);
        $this->assertSame(2, $mitra->verifikasi()->count());

        $baru = $mitra->verifikasiTerakhir;
        $this->assertNull($baru->keputusan);
        $this->assertNotSame('verifikasi_mitra/lama.pdf', $baru->bukti_pendukung);
        Storage::disk('local')->assertExists($baru->bukti_pendukung);

        // Pengajuan lama yang ditolak tetap tersimpan sebagai riwayat.
        $this->assertDatabaseHas('verifikasi_mitra', ['id_mitra' => $mitra->id_mitra, 'keputusan' => 'Ditolak']);
    }

    public function test_ajukan_ulang_tanpa_dokumen_memakai_dokumen_lama(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);

        $this->actingAs($mitra->pengguna)->post(route('profil-mitra.ajukan-ulang'))->assertSessionHas('sukses');

        $this->assertSame('verifikasi_mitra/lama.pdf', $mitra->fresh()->verifikasiTerakhir->bukti_pendukung);
        $this->assertSame(Mitra::STATUS_MENUNGGU, $mitra->fresh()->status_mitra);
    }

    public function test_dokumen_ajukan_ulang_divalidasi(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);

        $this->actingAs($mitra->pengguna)->post(route('profil-mitra.ajukan-ulang'), [
            'bukti_pendukung' => UploadedFile::fake()->create('nib.docx', 100, 'application/msword'),
        ])->assertSessionHasErrors(['bukti_pendukung' => 'Dokumen harus berformat PDF, JPG, atau PNG.']);

        $this->actingAs($mitra->pengguna)->post(route('profil-mitra.ajukan-ulang'), [
            'bukti_pendukung' => UploadedFile::fake()->create('nib.pdf', 5121, 'application/pdf'),
        ])->assertSessionHasErrors(['bukti_pendukung' => 'Ukuran dokumen maksimal 5 MB.']);

        $this->assertSame(Mitra::STATUS_DITOLAK, $mitra->fresh()->status_mitra);
        $this->assertSame(1, $mitra->verifikasi()->count());
    }

    public function test_hanya_mitra_ditolak_yang_bisa_mengajukan_ulang(): void
    {
        foreach ([Mitra::STATUS_MENUNGGU, Mitra::STATUS_TERVERIFIKASI, Mitra::STATUS_DITANGGUHKAN] as $i => $status) {
            $mitra = $this->buatMitra($status, "mitra{$i}@contoh.test");

            $this->actingAs($mitra->pengguna)
                ->post(route('profil-mitra.ajukan-ulang'))
                ->assertRedirect(route('profil-mitra.edit'))
                ->assertSessionHas('galat');

            $this->assertSame($status, $mitra->fresh()->status_mitra);
            $this->assertSame(1, $mitra->verifikasi()->count());
        }
    }

    public function test_tidak_bisa_mengajukan_ulang_dua_kali_berturut(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);

        $this->actingAs($mitra->pengguna)->post(route('profil-mitra.ajukan-ulang'))->assertSessionHas('sukses');
        $this->actingAs($mitra->pengguna)->post(route('profil-mitra.ajukan-ulang'))->assertSessionHas('galat');

        $this->assertSame(2, $mitra->verifikasi()->count());
    }

    public function test_pengajuan_ulang_masuk_antrean_admin_dengan_riwayat_lengkap(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);
        $this->actingAs($mitra->pengguna)->post(route('profil-mitra.ajukan-ulang'));
        $baru = $mitra->fresh()->verifikasiTerakhir;

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.index'))
            ->assertOk()->assertSee('Warung Bu Sari');

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.show', $baru))
            ->assertOk()
            ->assertSeeInOrder(['Pengajuan dikirim', 'Ditolak oleh', 'Dokumen NIB tidak terbaca', 'Pengajuan dikirim', 'Menunggu tinjauan admin'])
            ->assertSee('Setujui mitra');

        // Admin bisa menyetujui pengajuan ulang, mitra jadi terverifikasi.
        $this->actingAs($this->admin)->post(route('verifikasi-mitra.putuskan', $baru), ['keputusan' => 'setujui']);
        $this->assertTrue($mitra->fresh()->terverifikasi());
    }

    public function test_dashboard_mitra_ditolak_mengarah_ke_profil(): void
    {
        $mitra = $this->buatMitra(Mitra::STATUS_DITOLAK);

        $this->actingAs($mitra->pengguna)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Perbaiki dan ajukan ulang')
            ->assertSee('href="'.route('profil-mitra.edit').'"', false);
    }
}
