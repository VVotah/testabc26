<?php

namespace Tests\Feature;

use App\Models\KategoriMakanan;
use App\Models\Listing;
use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\TitikPengambilan;
use Database\Seeders\PeranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TitikPengambilanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PeranSeeder::class);
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

    private function buatMitra(string $email = 'sari@contoh.test', string $status = Mitra::STATUS_TERVERIFIKASI): Mitra
    {
        return Mitra::create([
            'id_pengguna' => $this->buatPengguna(Peran::MITRA, $email)->id_pengguna,
            'nama_mitra' => 'Warung '.$email,
            'jenis_mitra' => 'Restoran',
            'alamat' => Mitra::gabungAlamat('Jl. Dipatiukur No. 12', 'Bandung', '40132'),
            'status_mitra' => $status,
        ]);
    }

    private function buatTitik(Mitra $mitra, string $nama = 'Warung Utama'): TitikPengambilan
    {
        return $mitra->titikPengambilan()->create([
            'nama_titik' => $nama,
            'alamat' => 'Jl. Dipatiukur No. 12, Bandung',
            'patokan' => 'Pintu depan',
            'latitude' => -6.8915,
            'longitude' => 107.6159,
        ]);
    }

    private function dataTitik(array $ubah = []): array
    {
        return array_merge([
            'nama_titik' => 'Dapur Produksi',
            'alamat' => 'Jl. Buah Batu No. 88, Bandung',
            'patokan' => 'Pintu belakang, dekat parkiran',
            'latitude' => '-6.9395',
            'longitude' => '107.6236',
        ], $ubah);
    }

    /** Listing (modul 2) yang memakai titik, untuk menguji aturan hapus. */
    private function buatListing(TitikPengambilan $titik, string $status = Listing::STATUS_AKTIF): Listing
    {
        $kategori = KategoriMakanan::firstOrCreate(['nama_kategori' => 'Makanan berat']);

        return Listing::create([
            'id_mitra' => $titik->id_mitra,
            'id_titik' => $titik->id_titik,
            'id_kategori_makanan' => $kategori->id_kategori_makanan,
            'nama_makanan' => 'Nasi kotak',
            'foto' => 'listing/uji.jpg',
            'jumlah_porsi' => 10,
            'tipe_listing' => Listing::TIPE_GRATIS,
            'harga' => null,
            'batas_kedaluwarsa' => now()->addDay(),
            'status_listing' => $status,
        ]);
    }

    // ---------- Middleware peran Mitra/Donatur (FOOD-32) ----------

    public function test_middleware_peran_mitra_donatur_dengan_garis_miring(): void
    {
        Route::middleware(['web', 'auth', 'peran:Mitra/Donatur'])->get('/uji-khusus-mitra', fn () => 'ok');

        $this->actingAs($this->buatMitra()->pengguna)->get('/uji-khusus-mitra')->assertOk();

        foreach ([Peran::ADMIN, Peran::PENERIMA, Peran::RELAWAN] as $peran) {
            $this->actingAs($this->buatPengguna($peran, str()->slug($peran).'@contoh.test'))
                ->get('/uji-khusus-mitra')->assertForbidden();
        }
    }

    // ---------- Akses ----------

    public function test_tamu_dan_peran_lain_tidak_bisa_mengelola_titik(): void
    {
        $titik = $this->buatTitik($this->buatMitra());
        $urls = [
            route('titik-pengambilan.index'),
            route('titik-pengambilan.create'),
            route('titik-pengambilan.show', $titik),
            route('titik-pengambilan.edit', $titik),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        foreach ([Peran::ADMIN, Peran::PENERIMA, Peran::RELAWAN] as $peran) {
            $pengguna = $this->buatPengguna($peran, str()->slug($peran).'@contoh.test');
            foreach ($urls as $url) {
                $this->actingAs($pengguna)->get($url)->assertForbidden();
            }
            $this->actingAs($pengguna)->post(route('titik-pengambilan.store'), $this->dataTitik())->assertForbidden();
            $this->actingAs($pengguna)->delete(route('titik-pengambilan.destroy', $titik))->assertForbidden();
        }

        $this->assertDatabaseCount('titik_pengambilan', 1);
    }

    public function test_mitra_yang_belum_terverifikasi_tetap_boleh_mengelola_titik(): void
    {
        foreach ([Mitra::STATUS_MENUNGGU, Mitra::STATUS_DITOLAK] as $i => $status) {
            $mitra = $this->buatMitra("belum{$i}@contoh.test", $status);

            $this->actingAs($mitra->pengguna)->get(route('titik-pengambilan.index'))->assertOk();
            $this->actingAs($mitra->pengguna)
                ->post(route('titik-pengambilan.store'), $this->dataTitik())
                ->assertRedirect(route('titik-pengambilan.index'));

            $this->assertSame(1, $mitra->titikPengambilan()->count());
        }
    }

    public function test_titik_milik_mitra_lain_dijawab_404(): void
    {
        $titikOrangLain = $this->buatTitik($this->buatMitra('lain@contoh.test'));
        $saya = $this->buatMitra();

        $this->actingAs($saya->pengguna)->get(route('titik-pengambilan.show', $titikOrangLain))->assertNotFound();
        $this->actingAs($saya->pengguna)->get(route('titik-pengambilan.edit', $titikOrangLain))->assertNotFound();
        $this->actingAs($saya->pengguna)->put(route('titik-pengambilan.update', $titikOrangLain), $this->dataTitik())->assertNotFound();
        $this->actingAs($saya->pengguna)->delete(route('titik-pengambilan.destroy', $titikOrangLain))->assertNotFound();

        $this->assertSame('Warung Utama', $titikOrangLain->fresh()->nama_titik);
    }

    // ---------- Lihat ----------

    public function test_daftar_hanya_menampilkan_titik_milik_sendiri(): void
    {
        $saya = $this->buatMitra();
        $this->buatTitik($saya, 'Warung Utama');
        $this->buatTitik($this->buatMitra('lain@contoh.test'), 'Titik Orang Lain');

        $this->actingAs($saya->pengguna)->get(route('titik-pengambilan.index'))
            ->assertOk()
            ->assertSee('Warung Utama')
            ->assertSee('Pintu depan')
            ->assertDontSee('Titik Orang Lain');
    }

    public function test_daftar_kosong_mengajak_menambah_titik(): void
    {
        $this->actingAs($this->buatMitra()->pengguna)->get(route('titik-pengambilan.index'))
            ->assertOk()
            ->assertSee('Belum ada titik pengambilan')
            ->assertSee('Tambah titik pertama');
    }

    public function test_detail_menampilkan_koordinat_dan_tautan_peta(): void
    {
        $mitra = $this->buatMitra();
        $titik = $this->buatTitik($mitra);

        $this->actingAs($mitra->pengguna)->get(route('titik-pengambilan.show', $titik))
            ->assertOk()
            ->assertSee('-6.8915, 107.6159')
            ->assertSee('openstreetmap.org', false);
    }

    public function test_menu_sidebar_titik_pengambilan_aktif_untuk_mitra(): void
    {
        $this->actingAs($this->buatMitra()->pengguna)->get(route('titik-pengambilan.index'))
            ->assertSee('href="'.route('titik-pengambilan.index').'"', false);
    }

    // ---------- Tambah dan ubah ----------

    public function test_tambah_titik(): void
    {
        $mitra = $this->buatMitra();

        $this->actingAs($mitra->pengguna)
            ->post(route('titik-pengambilan.store'), $this->dataTitik())
            ->assertRedirect(route('titik-pengambilan.index'))
            ->assertSessionHas('sukses');

        $titik = $mitra->titikPengambilan()->first();
        $this->assertSame('Dapur Produksi', $titik->nama_titik);
        $this->assertSame('Pintu belakang, dekat parkiran', $titik->patokan);
        $this->assertEqualsWithDelta(-6.9395, $titik->latitude, 0.0000001);
        $this->assertEqualsWithDelta(107.6236, $titik->longitude, 0.0000001);
    }

    public function test_id_mitra_dari_form_diabaikan(): void
    {
        $orangLain = $this->buatMitra('lain@contoh.test');
        $saya = $this->buatMitra();

        $this->actingAs($saya->pengguna)->post(route('titik-pengambilan.store'), $this->dataTitik(['id_mitra' => $orangLain->id_mitra]));

        $this->assertSame(1, $saya->titikPengambilan()->count());
        $this->assertSame(0, $orangLain->titikPengambilan()->count());
    }

    public function test_koma_desimal_diterima_dan_patokan_opsional(): void
    {
        $mitra = $this->buatMitra();

        $this->actingAs($mitra->pengguna)
            ->post(route('titik-pengambilan.store'), $this->dataTitik(['latitude' => '-6,9395', 'longitude' => '107,6236', 'patokan' => '']))
            ->assertSessionHasNoErrors();

        $titik = $mitra->titikPengambilan()->first();
        $this->assertEqualsWithDelta(-6.9395, $titik->latitude, 0.0000001);
        $this->assertNull($titik->patokan);
    }

    public function test_validasi_field_wajib_dan_koordinat(): void
    {
        $mitra = $this->buatMitra();
        $kirim = fn (array $data) => $this->actingAs($mitra->pengguna)->post(route('titik-pengambilan.store'), $data);

        $kirim([])->assertSessionHasErrors(['nama_titik', 'alamat', 'latitude', 'longitude']);
        $kirim($this->dataTitik(['latitude' => '91']))->assertSessionHasErrors(['latitude' => 'Latitude harus di antara -90 dan 90.']);
        $kirim($this->dataTitik(['latitude' => '-90.5']))->assertSessionHasErrors('latitude');
        $kirim($this->dataTitik(['longitude' => '181']))->assertSessionHasErrors(['longitude' => 'Longitude harus di antara -180 dan 180.']);
        $kirim($this->dataTitik(['latitude' => 'abc']))->assertSessionHasErrors(['latitude' => 'Latitude harus berupa angka, mis. -6.8915.']);

        $this->assertSame(0, $mitra->titikPengambilan()->count());
    }

    public function test_nama_titik_tidak_boleh_kembar_untuk_mitra_yang_sama(): void
    {
        $saya = $this->buatMitra();
        $this->buatTitik($saya, 'Dapur Produksi');

        $this->actingAs($saya->pengguna)
            ->post(route('titik-pengambilan.store'), $this->dataTitik())
            ->assertSessionHasErrors(['nama_titik' => 'Kamu sudah punya titik dengan nama ini.']);

        // Mitra lain boleh memakai nama yang sama.
        $lain = $this->buatMitra('lain@contoh.test');
        $this->actingAs($lain->pengguna)->post(route('titik-pengambilan.store'), $this->dataTitik())->assertSessionHasNoErrors();
    }

    public function test_ubah_titik_dan_form_terisi(): void
    {
        $mitra = $this->buatMitra();
        $titik = $this->buatTitik($mitra);

        $this->actingAs($mitra->pengguna)->get(route('titik-pengambilan.edit', $titik))
            ->assertOk()
            ->assertSee('Ubah titik pengambilan')
            ->assertSee('value="Warung Utama"', false);

        // Menyimpan tanpa mengganti nama tidak dianggap nama kembar.
        $this->actingAs($mitra->pengguna)
            ->put(route('titik-pengambilan.update', $titik), $this->dataTitik(['nama_titik' => 'Warung Utama', 'patokan' => 'Pintu samping']))
            ->assertRedirect(route('titik-pengambilan.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Pintu samping', $titik->fresh()->patokan);
        $this->assertSame('Jl. Buah Batu No. 88, Bandung', $titik->fresh()->alamat);
    }

    // ---------- Hapus ----------

    public function test_hapus_titik(): void
    {
        $mitra = $this->buatMitra();
        $titik = $this->buatTitik($mitra);

        $this->actingAs($mitra->pengguna)
            ->delete(route('titik-pengambilan.destroy', $titik))
            ->assertRedirect(route('titik-pengambilan.index'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseMissing('titik_pengambilan', ['id_titik' => $titik->id_titik]);
    }

    public function test_hapus_ditolak_bila_titik_dipakai_listing_aktif(): void
    {
        $mitra = $this->buatMitra();
        $titik = $this->buatTitik($mitra);
        $this->buatListing($titik);
        $this->buatListing($titik);
        $this->buatListing($titik, Listing::STATUS_DITUTUP);

        $this->assertSame(2, $titik->cekTitikDipakaiListing());

        $this->actingAs($mitra->pengguna)
            ->from(route('titik-pengambilan.index'))
            ->delete(route('titik-pengambilan.destroy', $titik))
            ->assertRedirect(route('titik-pengambilan.index'))
            ->assertSessionHas('galat', fn ($pesan) => str_contains($pesan, '2 listing aktif'));

        $this->assertDatabaseHas('titik_pengambilan', ['id_titik' => $titik->id_titik]);
    }

    public function test_titik_yang_hanya_dipakai_listing_tidak_aktif_tidak_dihitung_tapi_tetap_tercatat(): void
    {
        $mitra = $this->buatMitra();
        $titik = $this->buatTitik($mitra);
        $this->buatListing($titik, Listing::STATUS_KEDALUWARSA);

        $this->assertSame(0, $titik->cekTitikDipakaiListing());

        // Riwayat listing masih merujuk titik lewat foreign key, jadi titik tidak ikut terhapus.
        $this->actingAs($mitra->pengguna)
            ->from(route('titik-pengambilan.index'))
            ->delete(route('titik-pengambilan.destroy', $titik))
            ->assertRedirect(route('titik-pengambilan.index'))
            ->assertSessionHas('galat', fn ($pesan) => str_contains($pesan, 'riwayat listing'));

        $this->assertDatabaseHas('titik_pengambilan', ['id_titik' => $titik->id_titik]);
    }

    public function test_cek_dipakai_listing_hanya_menghitung_titik_itu_sendiri(): void
    {
        $mitra = $this->buatMitra();
        $titik = $this->buatTitik($mitra);
        $titikLain = $this->buatTitik($mitra, 'Gerai Cabang');
        $this->buatListing($titikLain);

        $this->assertSame(0, $titik->cekTitikDipakaiListing());
        $this->assertSame(1, $titikLain->cekTitikDipakaiListing());
    }
}
