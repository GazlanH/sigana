<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pengumuman;
use App\Models\Kecamatan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SiganaFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test halaman utama publik dapat diakses
     */
    public function test_halaman_utama_publik_dapat_diakses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Papan Pengumuman Gangguan Aliran Air');
    }

    /**
     * Test halaman detail tiket dapat diakses
     */
    public function test_halaman_detail_tiket_dapat_diakses(): void
    {
        $pengumuman = Pengumuman::first();
        if ($pengumuman) {
            $response = $this->get('/detail/' . $pengumuman->nomor_tiket);
            $response->assertStatus(200);
            $response->assertSee($pengumuman->nomor_tiket);
        }
    }

    /**
     * Test halaman login admin
     */
    public function test_halaman_login_admin_dapat_diakses(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('SIGANA ADMIN');
    }

    /**
     * Test proteksi auth route admin
     */
    public function test_halaman_admin_dashboard_mencegah_tamu(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test admin login sukses
     */
    public function test_admin_bisa_login_dan_akses_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        $dashResponse = $this->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Operasional');
    }
}
