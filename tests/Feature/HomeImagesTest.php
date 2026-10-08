<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_home_page_image_is_streamed_from_private_storage_to_visitors(): void
    {
        Storage::fake('private');
        Storage::disk('private')->putFileAs('home', UploadedFile::fake()->image('hero-desktop.jpg'), 'hero-desktop.jpg');

        $this->get(route('private.home', 'hero-desktop.jpg'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');

        $this->get(route('private.home', 'missing.jpg'))->assertNotFound();
    }

    public function test_the_home_page_hero_shows_the_complete_slides_as_a_carousel(): void
    {
        Storage::fake('private');

        foreach (['sunset-desktop.jpg', 'sunset-mobile.jpg', 'fall-desktop.jpg', 'fall-mobile.jpg', 'winter-desktop.jpg'] as $name) {
            Storage::disk('private')->putFileAs('home', UploadedFile::fake()->image($name), $name);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('heroCarousel(2)', false) // Mobile and desktop heroes, two slides each
            ->assertSee('/private/home/sunset-desktop.jpg?v=', false)
            ->assertSee('/private/home/fall-mobile.jpg?v=', false)
            ->assertDontSee('winter-desktop.jpg') // No mobile image: not a slide yet
            ->assertDontSee('montreal_skyline');
    }

    public function test_the_home_page_hero_keeps_the_skyline_images_without_slides(): void
    {
        Storage::fake('private');

        $this->get('/')
            ->assertOk()
            ->assertSee('montreal_skyline-desktop.jpg')
            ->assertSee('montreal_skyline-mobile.jpg')
            ->assertDontSee('x-data="heroCarousel', false);
    }

    public function test_only_image_files_of_the_home_folder_are_reachable(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('home/notes.txt', 'not an image');
        Storage::disk('private')->put('secret.jpg', 'outside the home folder');

        $this->get('/private/home/notes.txt')->assertNotFound();
        $this->get('/private/home/..')->assertNotFound();
        $this->get('/private/home/..%2Fsecret.jpg')->assertNotFound();
    }
}
