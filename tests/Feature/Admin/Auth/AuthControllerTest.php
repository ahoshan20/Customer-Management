<?php

namespace Tests\Feature\Admin\Auth;

use App\Enums\ActiveInactive;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
    }

    public function test_non_admin_user_cannot_login_to_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->post(route('admin.login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_user_can_login_to_admin_panel(): void
    {
        $user = User::factory()->create();
        Admin::query()->create([
            'user_id' => $user->id,
            'status' => ActiveInactive::ACTIVE->value,
        ]);

        $this->post(route('admin.login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}
