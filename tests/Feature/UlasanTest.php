<?php

namespace Tests\Feature;

use App\Models\JadwalPickup;
use App\Models\Listing;
use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\Permintaan;
use App\Models\Ulasan;
use App\Models\TitikPengambilan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UlasanTest extends TestCase
{
    use RefreshDatabase;

    private Pengguna $penerima;
    private Pengguna $penerimaLain;
    private Pengguna $donatur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PeranSeeder::class);

        $this->penerima = $this->buatPengguna(Peran::PENERIMA);
        $this->penerimaLain = $this->buatPengguna(Peran::PENERIMA);
        $this->donatur = $this->buatPengguna(Peran::MITRA);
    }

    private function buatPengguna(string $namaPeran): Pengguna
    {
        $peran = Peran::where('nama_peran', $namaPeran)->firstOrFail();

        return Pengguna::create([
            'id_peran' => $peran->id_peran,
            'nama' => 'Pengguna Test',
            'email' => fake()->unique()->safeEmail(),
            'kata_sandi' => 'rahasia123',
            'status_akun' => 'Aktif',
        ]);
    }

    private function buatPickupSelesai(Pengguna $penerima): JadwalPickup
    {
        $mitra = Mitra::create([
            'id_pengguna' => $this->donatur->id_pengguna,
            'nama_mitra' => 'Mitra Test',
            'jenis_mitra' => 'Restoran',
            'alamat' => 'Bandung',
            'status_mitra' => 'Terverifikasi',
        ]);

        $titik = TitikPengambilan::create([
            'id_mitra' => $mitra->id_mitra,
            'nama_titik' => 'Titik Test',
            'alamat' => 'Bandung',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
        ]);

        $kategori = \App\Models\KategoriMakanan::create([
            'nama_kategori' => 'Makanan Test',
        ]);

        $listing = Listing::create([
            'id_mitra' => $mitra->id_mitra,
            'id_titik' => $titik->id_titik,
            'id_kategori_makanan' => $kategori->id_kategori_makanan,
            'nama_makanan' => 'Nasi Test',
            'foto' => 'test.jpg',
            'jumlah_porsi' => 5,
            'tipe_listing' => 'Gratis',
            'harga' => null,
            'batas_kedaluwarsa' => now()->addDay(),
            'status_listing' => 'Aktif',
        ]);

        $permintaan = Permintaan::create([
            'id_listing' => $listing->id_listing,
            'id_penerima' => $penerima->id_pengguna,
            'jumlah_porsi' => 1,
            'catatan' => null,
            'status_permintaan' => 'Disetujui',
        ]);

        return JadwalPickup::create([
            'id_permintaan' => $permintaan->id_permintaan,
            'id_titik' => $titik->id_titik,
            'waktu_pickup' => now(),
            'status_pickup' => 'Selesai',
            'catatan' => null,
        ]);
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('ulasan.index'))
            ->assertRedirect(route('login'));
    }

    public function test_penerima_dapat_membuka_halaman_ulasan(): void
    {
        $this->actingAs($this->penerima)
            ->get(route('ulasan.index'))
            ->assertOk();
    }

    public function test_pengguna_bukan_penerima_ditolak(): void
    {
        $this->actingAs($this->donatur)
            ->get(route('ulasan.index'))
            ->assertForbidden();
    }

    public function test_penerima_dapat_mengirim_ulasan(): void
    {
        $pickup = $this->buatPickupSelesai($this->penerima);

        $this->actingAs($this->penerima)
            ->post(route('ulasan.store', $pickup->id_jadwal_pickup), [
                'rating' => 5,
                'teks_ulasan' => 'Makanan diterima dengan baik.',
            ])
            ->assertRedirect(route('ulasan.index'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('ulasan', [
            'id_penerima' => $this->penerima->id_pengguna,
            'id_donatur' => $this->donatur->id_pengguna,
            'id_jadwal_pickup' => $pickup->id_jadwal_pickup,
            'rating' => 5,
            'teks_ulasan' => 'Makanan diterima dengan baik.',
        ]);
    }

    public function test_rating_di_luar_rentang_ditolak(): void
    {
        $pickup = $this->buatPickupSelesai($this->penerima);

        $this->actingAs($this->penerima)
            ->post(route('ulasan.store', $pickup->id_jadwal_pickup), [
                'rating' => 6,
                'teks_ulasan' => 'Ulasan test.',
            ])
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseMissing('ulasan', [
            'id_jadwal_pickup' => $pickup->id_jadwal_pickup,
        ]);
    }

    public function test_penerima_tidak_dapat_mengulas_pickup_milik_pengguna_lain(): void
    {
        $pickup = $this->buatPickupSelesai($this->penerimaLain);

        $this->actingAs($this->penerima)
            ->post(route('ulasan.store', $pickup->id_jadwal_pickup), [
                'rating' => 5,
                'teks_ulasan' => 'Ulasan tidak sah.',
            ])
            ->assertNotFound();

        $this->assertDatabaseMissing('ulasan', [
            'id_jadwal_pickup' => $pickup->id_jadwal_pickup,
        ]);
    }

    public function test_pickup_yang_sudah_diulas_tidak_dapat_diulas_kembali(): void
    {
        $pickup = $this->buatPickupSelesai($this->penerima);

        Ulasan::create([
            'id_penerima' => $this->penerima->id_pengguna,
            'id_donatur' => $this->donatur->id_pengguna,
            'id_jadwal_pickup' => $pickup->id_jadwal_pickup,
            'rating' => 4,
            'teks_ulasan' => 'Ulasan pertama.',
        ]);

        $this->actingAs($this->penerima)
            ->post(route('ulasan.store', $pickup->id_jadwal_pickup), [
                'rating' => 5,
                'teks_ulasan' => 'Ulasan kedua.',
            ])
            ->assertRedirect(route('ulasan.index'))
            ->assertSessionHas('galat');

        $this->assertDatabaseCount('ulasan', 1);
    }
}
