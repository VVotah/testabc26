<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Services\NotifikasiService;
use Database\Seeders\PeranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PeranSeeder::class);
    }

    private function buatPengguna(): Pengguna
    {
        return Pengguna::create([
            'id_peran' => Peran::where('nama_peran', Peran::PENERIMA)->value('id_peran'),
            'nama' => 'Pengguna Uji',
            'email' => fake()->unique()->safeEmail(),
            'kata_sandi' => 'rahasia123',
            'status_akun' => Pengguna::STATUS_AKTIF,
        ]);
    }

    private function buatNotifikasi(Pengguna $pengguna): Notifikasi
    {
        return Notifikasi::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'jenis_perubahan' => 'Status Permintaan',
            'id_objek' => 1,
            'isi_notifikasi' => 'Status permintaan makanan kamu telah diperbarui.',
        ]);
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        $this->get(route('notifikasi.index'))
            ->assertRedirect(route('login'));
    }

    public function test_pengguna_hanya_melihat_notifikasi_miliknya(): void
    {
        $pemilik = $this->buatPengguna();
        $penggunaLain = $this->buatPengguna();

        $notifikasiPemilik = $this->buatNotifikasi($pemilik);
        $notifikasiLain = $this->buatNotifikasi($penggunaLain);

        $notifikasiLain->update([
            'isi_notifikasi' => 'Ini notifikasi milik pengguna lain.',
        ]);

        $this->actingAs($pemilik)
            ->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee($notifikasiPemilik->isi_notifikasi)
            ->assertDontSee('Ini notifikasi milik pengguna lain.');
    }

    public function test_pengguna_bisa_melihat_detail_notifikasinya(): void
    {
        $pengguna = $this->buatPengguna();
        $notifikasi = $this->buatNotifikasi($pengguna);

        $this->actingAs($pengguna)
            ->get(route('notifikasi.show', $notifikasi->id_notifikasi))
            ->assertOk()
            ->assertSee($notifikasi->isi_notifikasi);
    }

    public function test_pengguna_tidak_bisa_melihat_detail_notifikasi_pengguna_lain(): void
    {
        $pemilik = $this->buatPengguna();
        $penggunaLain = $this->buatPengguna();

        $notifikasi = $this->buatNotifikasi($penggunaLain);

        $this->actingAs($pemilik)
            ->get(route('notifikasi.show', $notifikasi->id_notifikasi))
            ->assertNotFound();
    }

    public function test_pengguna_bisa_menghapus_notifikasinya(): void
    {
        $pengguna = $this->buatPengguna();
        $notifikasi = $this->buatNotifikasi($pengguna);

        $this->actingAs($pengguna)
            ->delete(route('notifikasi.destroy', $notifikasi->id_notifikasi))
            ->assertRedirect(route('notifikasi.index'));

        $this->assertDatabaseMissing('notifikasi', [
            'id_notifikasi' => $notifikasi->id_notifikasi,
        ]);
    }

    public function test_pengguna_tidak_bisa_menghapus_notifikasi_pengguna_lain(): void
    {
        $pemilik = $this->buatPengguna();
        $penggunaLain = $this->buatPengguna();

        $notifikasi = $this->buatNotifikasi($penggunaLain);

        $this->actingAs($pemilik)
            ->delete(route('notifikasi.destroy', $notifikasi->id_notifikasi))
            ->assertNotFound();

        $this->assertDatabaseHas('notifikasi', [
            'id_notifikasi' => $notifikasi->id_notifikasi,
        ]);
    }

    public function test_service_membuat_notifikasi_permintaan_baru(): void
    {
        $donatur = $this->buatPengguna();

        $notifikasi = app(NotifikasiService::class)->permintaanBaru(
            $donatur->id_pengguna,
            10
        );

        $this->assertDatabaseHas('notifikasi', [
            'id_notifikasi' => $notifikasi->id_notifikasi,
            'id_pengguna' => $donatur->id_pengguna,
            'jenis_perubahan' => 'Permintaan Baru',
            'id_objek' => 10,
        ]);
    }

    public function test_service_membuat_notifikasi_perubahan_status_permintaan(): void
    {
        $pengguna = $this->buatPengguna();

        $notifikasi = app(NotifikasiService::class)->statusPermintaanBerubah(
            $pengguna->id_pengguna,
            15,
            'Disetujui'
        );

        $this->assertDatabaseHas('notifikasi', [
            'id_notifikasi' => $notifikasi->id_notifikasi,
            'id_pengguna' => $pengguna->id_pengguna,
            'jenis_perubahan' => 'Status Permintaan',
            'id_objek' => 15,
            'isi_notifikasi' => 'Status permintaan makanan kamu telah berubah menjadi Disetujui.',
        ]);
    }

    public function test_service_membuat_notifikasi_perubahan_status_pickup(): void
    {
        $pengguna = $this->buatPengguna();

        $notifikasi = app(NotifikasiService::class)->statusPickupBerubah(
            $pengguna->id_pengguna,
            20,
            'Selesai'
        );

        $this->assertDatabaseHas('notifikasi', [
            'id_notifikasi' => $notifikasi->id_notifikasi,
            'id_pengguna' => $pengguna->id_pengguna,
            'jenis_perubahan' => 'Status Pickup',
            'id_objek' => 20,
            'isi_notifikasi' => 'Status pickup makanan kamu telah berubah menjadi Selesai.',
        ]);
    }
}
