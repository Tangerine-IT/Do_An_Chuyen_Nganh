<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_map_filters_active_locations_by_type(): void
    {
        $shop = Location::factory()->create(['name' => 'Cửa hàng thử nghiệm', 'type' => 'shop']);
        Location::factory()->create(['name' => 'Phòng khám thử nghiệm', 'type' => 'vet']);
        Location::factory()->create(['name' => 'Địa điểm ẩn', 'type' => 'pet_friendly', 'is_active' => false]);

        $this->get(route('map', ['type' => 'shop']))
            ->assertOk()
            ->assertSee($shop->name)
            ->assertDontSee('Phòng khám thử nghiệm')
            ->assertDontSee('Địa điểm ẩn');
    }

    public function test_admin_can_manage_locations_and_customers_are_forbidden(): void
    {
        $location = Location::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.locations.index'))->assertOk();
        $this->actingAs($admin)->put(route('admin.locations.update', $location), [
            'name' => 'Địa điểm cập nhật',
            'type' => 'vet',
            'address' => '1 Đường Mới',
            'latitude' => 10.8,
            'longitude' => 106.7,
            'is_active' => '1',
        ])->assertRedirect(route('admin.locations.index'));
        $this->assertDatabaseHas('locations', ['id' => $location->id, 'name' => 'Địa điểm cập nhật', 'type' => 'vet']);
        $this->actingAs(User::factory()->create())->get(route('admin.locations.index'))->assertForbidden();
    }
}
