<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\VerifikasiMitra;
use Database\Seeders\PeranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VerifikasiMitraTest extends TestCase
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

    /** Buat mitra beserta satu pengajuan. */
    private function buatPengajuan(string $namaUsaha = 'Warung Uji', ?string $keputusan = null, int $hariLalu = 1): VerifikasiMitra
    {
        $pengguna = $this->buatPengguna(Peran::MITRA, str()->slug($namaUsaha).'@contoh.test');

        $mitra = Mitra::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nama_mitra' => $namaUsaha,
            'jenis_mitra' => 'Restoran',
            'alamat' => Mitra::gabungAlamat('Jl. Dipatiukur No. 12', 'Bandung', '40132'),
            'status_mitra' => $keputusan ?? Mitra::STATUS_MENUNGGU,
        ]);

        $berkas = 'verifikasi_mitra/'.$mitra->id_mitra.'.pdf';
        Storage::disk('local')->put($berkas, '%PDF-1.4 uji');

        return VerifikasiMitra::create([
            'id_mitra' => $mitra->id_mitra,
            'bukti_pendukung' => $berkas,
            'tanggal_pengajuan' => now()->subDays($hariLalu),
            'keputusan' => $keputusan,
            'id_admin' => $keputusan ? $this->admin->id_pengguna : null,
            'tanggal_keputusan' => $keputusan ? now() : null,
        ]);
    }

    // ---------- Akses ----------

    public function test_hanya_admin_yang_bisa_membuka_halaman_verifikasi(): void
    {
        $pengajuan = $this->buatPengajuan();
        $urls = [
            route('verifikasi-mitra.index'),
            route('verifikasi-mitra.show', $pengajuan),
            route('verifikasi-mitra.dokumen', $pengajuan),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        foreach ([Peran::MITRA, Peran::PENERIMA, Peran::RELAWAN] as $peran) {
            $pengguna = $this->buatPengguna($peran, str()->slug($peran).'.lain@contoh.test');
            foreach ($urls as $url) {
                $this->actingAs($pengguna)->get($url)->assertForbidden();
            }
            $this->actingAs($pengguna)
                ->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'setujui'])
                ->assertForbidden();
        }

        $this->assertNull($pengajuan->fresh()->keputusan);
    }

    // ---------- Daftar ----------

    public function test_daftar_menampilkan_yang_menunggu_lebih_dulu_dan_bisa_disaring(): void
    {
        $this->buatPengajuan('Bakery Roti Kita', null, 1);
        $this->buatPengajuan('Katering Nusantara', null, 3);
        $this->buatPengajuan('Warung Bu Sari', VerifikasiMitra::KEPUTUSAN_SETUJU);
        $this->buatPengajuan('Panitia Festival', VerifikasiMitra::KEPUTUSAN_TOLAK);

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.index'))
            ->assertOk()
            ->assertSeeInOrder(['Katering Nusantara', 'Bakery Roti Kita']) // terlama di atas
            ->assertDontSee('Warung Bu Sari')
            ->assertDontSee('Panitia Festival');

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.index', ['status' => 'terverifikasi']))
            ->assertOk()->assertSee('Warung Bu Sari')->assertDontSee('Bakery Roti Kita');

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.index', ['status' => 'ditolak']))
            ->assertOk()->assertSee('Panitia Festival')->assertDontSee('Warung Bu Sari');
    }

    public function test_daftar_kosong_memberi_keterangan(): void
    {
        $this->actingAs($this->admin)->get(route('verifikasi-mitra.index'))
            ->assertOk()->assertSee('Tidak ada pengajuan yang menunggu');
    }

    public function test_menu_sidebar_verifikasi_mitra_aktif_untuk_admin(): void
    {
        $this->actingAs($this->admin)->get(route('verifikasi-mitra.index'))
            ->assertSee('href="'.route('verifikasi-mitra.index').'"', false);
    }

    // ---------- Tinjau dan dokumen ----------

    public function test_halaman_tinjau_menampilkan_data_riwayat_dan_form_keputusan(): void
    {
        $pengajuan = $this->buatPengajuan('Warung Uji');

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.show', $pengajuan))
            ->assertOk()
            ->assertSee('Tinjau pengajuan mitra')
            ->assertSee('Warung Uji')
            ->assertSee('Jl. Dipatiukur No. 12')
            ->assertSee('Pengajuan dikirim')
            ->assertSee('Menunggu tinjauan admin')
            ->assertSee('Setujui mitra')
            ->assertSee(route('verifikasi-mitra.dokumen', $pengajuan), false);
    }

    public function test_dokumen_dikirim_untuk_admin_dari_disk_privat(): void
    {
        $pengajuan = $this->buatPengajuan();

        $respons = $this->actingAs($this->admin)->get(route('verifikasi-mitra.dokumen', $pengajuan));

        $respons->assertOk();
        $this->assertStringContainsString('inline', $respons->headers->get('content-disposition'));
        $this->assertSame('nosniff', $respons->headers->get('x-content-type-options'));
    }

    public function test_dokumen_yang_hilang_memberi_404(): void
    {
        $pengajuan = $this->buatPengajuan();
        Storage::disk('local')->delete($pengajuan->bukti_pendukung);

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.dokumen', $pengajuan))->assertNotFound();
        $this->actingAs($this->admin)->get('/verifikasi-mitra/9999')->assertNotFound();
    }

    // ---------- Keputusan ----------

    public function test_setujui_mengubah_pengajuan_dan_status_mitra(): void
    {
        $pengajuan = $this->buatPengajuan('Warung Uji');

        $this->actingAs($this->admin)
            ->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'setujui'])
            ->assertRedirect(route('verifikasi-mitra.index'))
            ->assertSessionHas('sukses');

        $pengajuan->refresh();
        $this->assertSame(VerifikasiMitra::KEPUTUSAN_SETUJU, $pengajuan->keputusan);
        $this->assertSame($this->admin->id_pengguna, $pengajuan->id_admin);
        $this->assertNotNull($pengajuan->tanggal_keputusan);
        $this->assertNull($pengajuan->alasan);
        $this->assertSame(Mitra::STATUS_TERVERIFIKASI, $pengajuan->mitra->status_mitra);
        $this->assertTrue($pengajuan->mitra->terverifikasi());
    }

    public function test_tolak_wajib_alasan_dan_mengubah_status_mitra(): void
    {
        $pengajuan = $this->buatPengajuan();

        $this->actingAs($this->admin)
            ->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'tolak', 'alasan' => ''])
            ->assertSessionHasErrors(['alasan' => 'Alasan penolakan wajib diisi.']);
        $this->assertNull($pengajuan->fresh()->keputusan);
        $this->assertSame(Mitra::STATUS_MENUNGGU, $pengajuan->mitra->fresh()->status_mitra);

        $this->actingAs($this->admin)
            ->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'tolak', 'alasan' => 'Dokumen tidak terbaca'])
            ->assertRedirect(route('verifikasi-mitra.index'));

        $pengajuan->refresh();
        $this->assertSame(VerifikasiMitra::KEPUTUSAN_TOLAK, $pengajuan->keputusan);
        $this->assertSame('Dokumen tidak terbaca', $pengajuan->alasan);
        $this->assertSame(Mitra::STATUS_DITOLAK, $pengajuan->mitra->status_mitra);
    }

    public function test_keputusan_tidak_dikenal_ditolak(): void
    {
        $pengajuan = $this->buatPengajuan();

        $this->actingAs($this->admin)
            ->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'Terverifikasi'])
            ->assertSessionHasErrors('keputusan');

        $this->assertNull($pengajuan->fresh()->keputusan);
    }

    public function test_pengajuan_tidak_bisa_diputuskan_dua_kali(): void
    {
        $pengajuan = $this->buatPengajuan();

        $this->actingAs($this->admin)->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'setujui']);

        $this->actingAs($this->admin)
            ->post(route('verifikasi-mitra.putuskan', $pengajuan), ['keputusan' => 'tolak', 'alasan' => 'Berubah pikiran'])
            ->assertSessionHasErrors(['keputusan' => 'Pengajuan ini sudah diputuskan sebelumnya.']);

        $this->assertSame(VerifikasiMitra::KEPUTUSAN_SETUJU, $pengajuan->fresh()->keputusan);
        $this->assertSame(Mitra::STATUS_TERVERIFIKASI, $pengajuan->mitra->fresh()->status_mitra);
    }

    public function test_pengajuan_yang_sudah_diputuskan_menampilkan_ringkasan_tanpa_form(): void
    {
        $pengajuan = $this->buatPengajuan('Panitia Uji', VerifikasiMitra::KEPUTUSAN_TOLAK);
        $pengajuan->update(['alasan' => 'Dokumen tidak terbaca']);

        $this->actingAs($this->admin)->get(route('verifikasi-mitra.show', $pengajuan))
            ->assertOk()
            ->assertSee('Dokumen tidak terbaca')
            ->assertDontSee('Setujui mitra');
    }

    // ---------- Dashboard ----------

    public function test_dashboard_admin_menampilkan_antrean_terlama(): void
    {
        $this->buatPengajuan('Bakery Roti Kita', null, 1);
        $this->buatPengajuan('Katering Nusantara', null, 2);
        $this->buatPengajuan('Kantin FRI', null, 0);
        $this->buatPengajuan('Toko Keempat', null, 0);

        $this->actingAs($this->admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Katering Nusantara', 'Bakery Roti Kita'])
            ->assertSee('Lihat semua')
            ->assertSee(route('verifikasi-mitra.index'), false);
    }

    public function test_dashboard_mitra_ditolak_menampilkan_alasan(): void
    {
        $pengajuan = $this->buatPengajuan('Warung Uji');
        $pengajuan->tolak($this->admin, 'Dokumen NIB tidak terbaca');

        $this->actingAs($pengajuan->mitra->pengguna)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Dokumen NIB tidak terbaca');
    }

    // ---------- Middleware mitra.terverifikasi ----------

    public function test_middleware_hanya_meloloskan_mitra_terverifikasi(): void
    {
        Route::middleware(['web', 'auth', 'mitra.terverifikasi'])->get('/uji-khusus-mitra-terverifikasi', fn () => 'ok');

        $menunggu = $this->buatPengajuan('Warung Menunggu');
        $setuju = $this->buatPengajuan('Warung Setuju', VerifikasiMitra::KEPUTUSAN_SETUJU);
        $tanpaMitra = $this->buatPengguna(Peran::PENERIMA, 'penerima@contoh.test');

        $this->actingAs($menunggu->mitra->pengguna)->get('/uji-khusus-mitra-terverifikasi')
            ->assertRedirect(route('dashboard'))->assertSessionHas('galat');

        $this->actingAs($tanpaMitra)->get('/uji-khusus-mitra-terverifikasi')
            ->assertRedirect(route('dashboard'));

        $this->actingAs($setuju->mitra->pengguna)->get('/uji-khusus-mitra-terverifikasi')
            ->assertOk()->assertSee('ok');
    }
}
