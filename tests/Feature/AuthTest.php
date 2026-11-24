<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Peran;
use Database\Seeders\PeranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PeranSeeder::class);
    }

    private function buatPengguna(string $peran, string $status = Pengguna::STATUS_AKTIF): Pengguna
    {
        return Pengguna::create([
            'id_peran' => Peran::where('nama_peran', $peran)->value('id_peran'),
            'nama' => 'Uji '.$peran,
            'email' => fake()->unique()->safeEmail(),
            'kata_sandi' => 'rahasia123',
            'status_akun' => $status,
        ]);
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()->assertSee('Masuk');
    }

    public function test_masuk_dengan_kredensial_benar(): void
    {
        $pengguna = $this->buatPengguna(Peran::PENERIMA);

        $this->post(route('login.proses'), ['email' => $pengguna->email, 'kata_sandi' => 'rahasia123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($pengguna);
    }

    public function test_kata_sandi_salah_ditolak(): void
    {
        $pengguna = $this->buatPengguna(Peran::PENERIMA);

        $this->from(route('login'))
            ->post(route('login.proses'), ['email' => $pengguna->email, 'kata_sandi' => 'salah'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_field_kosong_ditolak(): void
    {
        $this->post(route('login.proses'), [])->assertSessionHasErrors(['email', 'kata_sandi']);
    }

    public function test_akun_ditangguhkan_tidak_bisa_masuk(): void
    {
        $pengguna = $this->buatPengguna(Peran::PENERIMA, Pengguna::STATUS_DITANGGUHKAN);

        $this->post(route('login.proses'), ['email' => $pengguna->email, 'kata_sandi' => 'rahasia123'])
            ->assertSessionHasErrors(['email' => 'Akun kamu ditangguhkan. Hubungi Admin FoodLoop.']);

        $this->assertGuest();
    }

    public function test_akun_yang_ditangguhkan_saat_login_dikeluarkan(): void
    {
        $pengguna = $this->buatPengguna(Peran::PENERIMA);
        $this->actingAs($pengguna)->get('/dashboard')->assertOk();

        $pengguna->update(['status_akun' => Pengguna::STATUS_DITANGGUHKAN]);

        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_percobaan_masuk_dibatasi(): void
    {
        $pengguna = $this->buatPengguna(Peran::PENERIMA);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.proses'), ['email' => $pengguna->email, 'kata_sandi' => 'salah']);
        }

        $this->post(route('login.proses'), ['email' => $pengguna->email, 'kata_sandi' => 'rahasia123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_keluar(): void
    {
        $this->actingAs($this->buatPengguna(Peran::RELAWAN))
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_dashboard_tampil_sesuai_peran(): void
    {
        $harapan = [
            Peran::ADMIN => ['Dashboard Admin', 'Verifikasi Mitra'],
            Peran::MITRA => ['Dashboard Mitra', 'Titik Pengambilan'],
            Peran::PENERIMA => ['Dashboard Penerima', 'Cari Makanan'],
            Peran::RELAWAN => ['Dashboard Relawan', 'Tugas Pengantaran'],
        ];

        foreach ($harapan as $peran => $teks) {
            $this->actingAs($this->buatPengguna($peran))
                ->get('/dashboard')
                ->assertOk()
                ->assertSeeInOrder([$teks[1], $teks[0]]);
        }
    }

    public function test_middleware_peran_membatasi_akses(): void
    {
        Route::middleware(['web', 'auth', 'peran:Admin'])->get('/uji-khusus-admin', fn () => 'ok');
        Route::middleware(['web', 'auth', 'peran:Penerima,Relawan'])->get('/uji-dua-peran', fn () => 'ok');

        $this->actingAs($this->buatPengguna(Peran::ADMIN))->get('/uji-khusus-admin')->assertOk();
        $this->actingAs($this->buatPengguna(Peran::MITRA))->get('/uji-khusus-admin')->assertForbidden();
        $this->actingAs($this->buatPengguna(Peran::RELAWAN))->get('/uji-dua-peran')->assertOk();
        $this->actingAs($this->buatPengguna(Peran::ADMIN))->get('/uji-dua-peran')->assertForbidden();
    }
}
