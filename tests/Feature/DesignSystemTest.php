<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_design_gallery_is_not_exposed_outside_the_local_environment(): void
    {
        $this->get('/design')->assertNotFound();
        $this->get('/design/admin')->assertNotFound();
    }

    public function test_the_whole_gallery_renders_every_component(): void
    {
        $product = Product::factory()->state(['price' => 300000])->onSale(10)->create();
        ProductImage::factory()->for($product)->primary()->create();

        $html = view('design.index', [
            'products' => Product::with(['primaryImage', 'brand'])->withRatingSummary()->get(),
            'paginator' => new LengthAwarePaginator(range(1, 10), 87, 10, 3, ['path' => url('/design')]),
        ])->render();

        $this->assertStringContainsString(__('ui.design.title'), $html);
        $this->assertStringContainsString('aria-busy="true"', $html);
        $this->assertDoesNotMatchRegularExpression('/\b(ui|enums)\.[a-z_]+\.[a-z_.]+/', strip_tags($html));
    }

    public function test_the_admin_layout_renders_with_disabled_navigation_for_unbuilt_pages(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $html = view('design.admin')->render();

        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringContainsString(__('ui.admin.coming_soon'), $html);
        $this->assertStringContainsString('id="main"', $html);
    }

    public function test_auth_pages_use_the_branded_guest_layout(): void
    {
        $this->get(route('login'))->assertOk()->assertSee(__('ui.auth.login_title'))->assertSee('id="main"', false);
        $this->get(route('register'))->assertOk()->assertSee(__('ui.auth.register_title'));
        $this->get(route('password.request'))->assertOk()->assertSee(__('ui.auth.forgot_title'));
    }

    public function test_price_shows_the_sale_price_first_and_strikes_the_original(): void
    {
        $this->blade('<x-price :price="329000" :sale-price="289000" />')
            ->assertSee(format_vnd(289000))
            ->assertSee('<del', false)
            ->assertSee(format_vnd(329000));
    }

    public function test_price_ignores_a_sale_price_that_is_not_lower(): void
    {
        $html = (string) $this->blade('<x-price :price="100000" :sale-price="100000" />');

        $this->assertStringNotContainsString('<del', $html);
        $this->assertStringContainsString(format_vnd(100000), $html);
    }

    public function test_price_renders_nothing_without_a_price(): void
    {
        $this->assertSame('', trim((string) $this->blade('<x-price />')));
    }

    public function test_rating_stars_expose_an_accessible_label_and_review_count(): void
    {
        $this->blade('<x-rating-stars :rating="4.3" :count="42" />')
            ->assertSee(__('ui.rating.label', ['rating' => '4,3']), false)
            ->assertSee('(42)');
    }

    public function test_rating_stars_are_clamped_to_the_valid_range(): void
    {
        $this->blade('<x-rating-stars :rating="9" />')->assertSee(__('ui.rating.label', ['rating' => '5,0']), false);
        $this->blade('<x-rating-stars :rating="-3" />')->assertSee(__('ui.rating.label', ['rating' => '0,0']), false);
    }

    public function test_button_renders_a_link_when_given_an_href_and_a_button_otherwise(): void
    {
        $this->blade('<x-button href="/x" variant="accent">Đi</x-button>')
            ->assertSee('<a href="/x"', false);

        $this->blade('<x-button type="submit">Gửi</x-button>')
            ->assertSee('<button type="submit"', false);
    }

    public function test_button_falls_back_to_the_default_variant_and_size_for_unknown_values(): void
    {
        $this->blade('<x-button variant="nope" size="nope">Ok</x-button>')
            ->assertSee('bg-primary-700', false)
            ->assertSee('px-6', false);
    }

    public function test_pet_badge_accepts_enums_and_plain_strings(): void
    {
        $this->blade('<x-pet-badge :species="App\Enums\PetSpecies::Dog" />')->assertSee('Chó');
        $this->blade('<x-pet-badge species="all" />')->assertSee('Mọi loài');
    }

    public function test_input_wires_label_value_and_validation_errors_together(): void
    {
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['email' => ['Email không hợp lệ.']]));

        $html = (string) $this->withViewErrors($errors->getBag('default')->getMessages())
            ->blade('<x-input name="email" type="email" label="Email" value="a@b" required />');

        $this->assertStringContainsString('for="email"', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('aria-describedby="email-error"', $html);
        $this->assertStringContainsString('Email không hợp lệ.', $html);
        $this->assertStringContainsString('required', $html);
    }

    public function test_password_inputs_never_echo_a_value(): void
    {
        $html = (string) $this->blade('<x-input name="password" type="password" value="secret" />');

        $this->assertStringNotContainsString('secret', $html);
    }

    public function test_pagination_is_localized_and_marks_the_current_page(): void
    {
        $html = (string) (new LengthAwarePaginator(range(1, 10), 87, 10, 3, ['path' => '/x']))->links();

        $this->assertStringContainsString('Hiển thị 21-30 trong 87 kết quả', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString(__('ui.pagination.next'), $html);
    }

    public function test_pagination_renders_nothing_for_a_single_page(): void
    {
        $this->assertSame('', trim((string) (new LengthAwarePaginator(range(1, 3), 3, 10, 1, ['path' => '/x']))->links()));
    }

    public function test_empty_state_shows_its_action_only_when_provided(): void
    {
        $with = (string) $this->blade('<x-empty-state title="Trống"><a href="/a">Làm gì đó</a></x-empty-state>');
        $without = (string) $this->blade('<x-empty-state title="Trống" />');

        $this->assertStringContainsString('Làm gì đó', $with);
        $this->assertStringNotContainsString('mt-6', $without);
    }

    public function test_toast_container_turns_session_flashes_into_toasts(): void
    {
        $this->withSession(['success' => 'Đã lưu thành công'])
            ->blade('<x-toast-container />')
            ->assertSee('Đã lưu thành công');
    }
}
