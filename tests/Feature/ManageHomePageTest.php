<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageHomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_shows_for_management_roles_only(): void
    {
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
}
