<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DevelopmentModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('admin');
        Role::findOrCreate('bidder');
    }

    public function test_admin_can_update_development_mode_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'development_mode' => '1',
            'development_mode_message' => 'المنصة حالياً قيد التطوير التجريبي',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('1', Setting::get('development_mode'));
        $this->assertEquals('المنصة حالياً قيد التطوير التجريبي', Setting::get('development_mode_message'));
    }

    public function test_development_banner_is_displayed_when_enabled(): void
    {
        Setting::set('development_mode', '1');
        Setting::set('development_mode_message', 'المنصة في وضع التطوير حالياً');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('motorzad-dev-banner');
        $response->assertSee('المنصة في وضع التطوير حالياً');
        $response->assertSee('وضع التطوير');
    }

    public function test_development_banner_is_hidden_when_disabled(): void
    {
        Setting::set('development_mode', '0');
        Setting::set('development_mode_message', 'المنصة في وضع التطوير حالياً');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('motorzad-dev-banner');
    }

    public function test_development_banner_appears_in_admin_dashboard(): void
    {
        Setting::set('development_mode', '1');
        Setting::set('development_mode_message', 'رسالة تنبيهية خاصة باللوحة');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('motorzad-dev-banner');
        $response->assertSee('رسالة تنبيهية خاصة باللوحة');
    }
}
