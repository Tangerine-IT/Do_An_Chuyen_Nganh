<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_email_is_accepted_with_a_success_toast(): void
    {
        $this->post(route('newsletter.subscribe'), ['email' => 'ban@example.com'])
            ->assertRedirect(route('home').'#newsletter')
            ->assertSessionHas('success', __('ui.newsletter.success'))
            ->assertSessionHasNoErrors();
    }

    public function test_an_invalid_email_is_rejected_and_the_visitor_returns_to_the_strip(): void
    {
        $this->from(route('home'))
            ->post(route('newsletter.subscribe'), ['email' => 'khong-hop-le'])
            ->assertRedirect(route('home').'#newsletter')
            ->assertSessionHasErrors('email');
    }

    public function test_the_email_is_required(): void
    {
        $this->post(route('newsletter.subscribe'), [])->assertSessionHasErrors('email');
    }

    public function test_the_endpoint_is_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->post(route('newsletter.subscribe'), ['email' => 'ban@example.com'])->assertRedirect();
        }

        $this->post(route('newsletter.subscribe'), ['email' => 'ban@example.com'])->assertStatus(429);
    }
}
