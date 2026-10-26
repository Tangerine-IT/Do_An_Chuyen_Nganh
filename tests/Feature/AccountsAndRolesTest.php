<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountsAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_cannot_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk();
    }

    public function test_staff_cannot_open_admin_dashboard_until_admin_routes_are_enabled(): void
    {
        $this->actingAs(User::factory()->staff()->create())->get('/admin')->assertForbidden();
    }

    public function test_login_redirects_admin_to_admin_dashboard_and_customer_home(): void
    {
        $customer = User::factory()->create(['email' => 'customer@example.com']);
        $this->post('/login', ['email' => $customer->email, 'password' => 'password'])->assertRedirect(route('home', absolute: false));
        auth()->logout();

        $admin = User::factory()->admin()->create(['email' => 'admin@example.com']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_login_ignores_customer_intended_url_and_opens_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin-intended@example.com']);
        $this->withSession(['url.intended' => route('account.pets')])
            ->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_staff_login_opens_booking_management(): void
    {
        $staff = User::factory()->staff()->create(['email' => 'staff@example.com']);

        $this->post('/login', ['email' => $staff->email, 'password' => 'password'])
            ->assertRedirect(route('admin.bookings.index', absolute: false));
    }

    public function test_user_cannot_delete_another_users_address(): void
    {
        $owner = User::factory()->create();
        $address = Address::factory()->for($owner)->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('account.addresses.destroy', $address))
            ->assertForbidden();
    }

    public function test_user_cannot_view_another_users_domain_records(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->assertFalse($other->can('view', Pet::factory()->for($owner)->create()));
        $this->assertFalse($other->can('view', Order::factory()->for($owner)->create()));
        $this->assertFalse($other->can('view', Booking::factory()->for($owner)->create()));
    }

    public function test_first_address_is_default_and_deleting_default_promotes_latest(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('account.addresses.store'), [
            'label' => 'Nhà',
            'recipient_name' => 'Người nhận',
            'phone' => '0901234567',
            'address_line' => '1 Đường A',
            'province' => 'TP. Hồ Chí Minh',
        ]);
        $first = $user->addresses()->first();
        $second = Address::factory()->for($user)->create();

        $this->assertTrue($first->fresh()->is_default);
        $this->actingAs($user)->delete(route('account.addresses.destroy', $first))->assertRedirect();
        $this->assertTrue($second->fresh()->is_default);
    }

    public function test_customer_can_create_and_delete_their_pet_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account.pets'))->assertOk();
        $this->actingAs($user)->post(route('account.pets.store'), [
            'name' => 'Milo',
            'species' => 'dog',
            'breed' => 'Poodle',
            'sex' => 'male',
            'allergies' => 'Gà, sữa',
            'notes' => 'Cần theo dõi cân nặng',
        ])->assertRedirect();

        $pet = $user->pets()->first();
        $this->assertSame(['Gà', 'sữa'], $pet->allergies);

        $this->actingAs($user)->delete(route('account.pets.destroy', $pet))->assertRedirect();
        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
    }

    public function test_customer_cannot_delete_another_users_pet(): void
    {
        $owner = User::factory()->create();
        $pet = Pet::factory()->for($owner)->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('account.pets.destroy', $pet))
            ->assertForbidden();
    }

    public function test_customer_account_navigation_opens_account_and_pet_profiles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account'))->assertOk();
        $this->actingAs($user)->get(route('account.pets'))->assertOk();
    }
}
