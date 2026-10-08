<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageHomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_shows_for_management_roles_only(): void
    {
        Storage::fake('private');

        $this->actingAs(User::factory()->create(['role' => Role::ADMIN]))
            ->get(route('manage-home-page'))
            ->assertOk()
            ->assertSee("Gérer la page d'accueil") // The test locale is fr_CA
            ->assertSee('fa-house');

        $this->actingAs(User::factory()->create(['role' => Role::MEMBER]))
            ->get(route('manage-home-page'))
            ->assertForbidden();
    }

    public function test_the_feature_button_shows_on_the_other_dashboard_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => Role::ADMIN]))
            ->get(route('manage-prayer-topics'))
            ->assertOk()
            ->assertSee('href="/gerer-page-accueil"', false);
    }

    public function test_hero_images_are_uploaded_listed_and_deleted_as_a_slide(): void
    {
        Storage::fake('private');
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->post(route('hero-images.store'), [
                'desktop' => UploadedFile::fake()->image('Montréal Sunset-desktop.JPG', 1920, 1080),
                'mobile' => UploadedFile::fake()->image('Montréal Sunset-mobile.jpg', 1080, 1350),
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        Storage::disk('private')->assertExists(['home/montreal-sunset-desktop.jpg', 'home/montreal-sunset-mobile.jpg']);

        // Listed in the Image Library and shown in the preview, from the private.home route
        $this->actingAs($admin)
            ->get(route('manage-home-page'))
            ->assertOk()
            ->assertSee('montreal-sunset-desktop.jpg')
            ->assertSee('/private/home/montreal-sunset-mobile.jpg?v=', false);

        // An image of the same name replaces the current one, whatever its extension
        $this->actingAs($admin)
            ->post(route('hero-images.store'), ['desktop' => UploadedFile::fake()->image('montreal-sunset-desktop.png')])
            ->assertSessionHasNoErrors();

        Storage::disk('private')->assertExists('home/montreal-sunset-desktop.png');
        Storage::disk('private')->assertMissing('home/montreal-sunset-desktop.jpg');

        $this->actingAs($admin)->delete(route('hero-images.destroy', 'montreal-sunset'))->assertSessionHas('status');

        $this->assertSame([], Storage::disk('private')->files('home'));

        $this->actingAs($admin)->delete(route('hero-images.destroy', 'montreal-sunset'))->assertNotFound();
    }

    public function test_hero_image_uploads_are_validated_and_reserved_for_management_roles(): void
    {
        Storage::fake('private');
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        // Wrong ending for its drop zone, nothing chosen, not an image
        $this->actingAs($admin)
            ->post(route('hero-images.store'), ['desktop' => UploadedFile::fake()->image('montreal-sunset-mobile.jpg')])
            ->assertSessionHasErrorsIn('uploadHeroImages', 'desktop');

        $this->actingAs($admin)
            ->post(route('hero-images.store'), [])
            ->assertSessionHasErrorsIn('uploadHeroImages', 'desktop');

        $this->actingAs($admin)
            ->post(route('hero-images.store'), ['mobile' => UploadedFile::fake()->create('notes-mobile.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrorsIn('uploadHeroImages', 'mobile');

        $member = User::factory()->create(['role' => Role::MEMBER]);

        $this->actingAs($member)
            ->post(route('hero-images.store'), ['desktop' => UploadedFile::fake()->image('montreal-sunset-desktop.jpg')])
            ->assertForbidden();

        $this->actingAs($member)->delete(route('hero-images.destroy', 'montreal-sunset'))->assertForbidden();

        $this->assertSame([], Storage::disk('private')->files('home'));
    }
}
