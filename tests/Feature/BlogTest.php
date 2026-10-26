<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_blog_filters_published_posts_and_shows_article_details(): void
    {
        $published = Post::factory()->create(['category' => 'Sức khỏe', 'title' => 'Lịch tiêm phòng']);
        $other = Post::factory()->create(['category' => 'Dinh dưỡng']);
        Post::factory()->draft()->create();

        $this->get(route('blog.index', ['category' => 'Sức khỏe']))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($other->title);

        $this->get(route('blog.show', $published->slug))
            ->assertOk()
            ->assertSee($published->title)
            ->assertSee('og:type', false)
            ->assertSee('phút đọc');
    }

    public function test_customer_cannot_manage_posts_and_admin_can_upload_and_publish(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create();
        $this->actingAs($customer)->get(route('admin.posts.index'))->assertForbidden();

        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Chăm sóc răng miệng',
            'category' => 'Sức khỏe',
            'body' => '# Hướng dẫn',
            'status' => PostStatus::Published->value,
            'cover_image' => UploadedFile::fake()->create('cover.jpg', 10, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $post = Post::query()->where('title', 'Chăm sóc răng miệng')->firstOrFail();
        $this->assertSame(PostStatus::Published, $post->status);
        Storage::disk('public')->assertExists($post->cover_image_path);
    }
}
