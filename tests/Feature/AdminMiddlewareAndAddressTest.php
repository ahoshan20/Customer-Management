<?php

namespace Tests\Feature;

use App\Enums\ActiveInactive;
use App\Models\Address;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminMiddlewareAndAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_user_cannot_pass_admin_middleware(): void
    {
        Route::middleware(['web', 'auth', 'admin'])->get('/test-admin-protected', fn () => response('ok'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/test-admin-protected')
            ->assertForbidden();
    }

    public function test_active_admin_user_can_pass_admin_middleware(): void
    {
        Route::middleware(['web', 'auth', 'admin'])->get('/test-active-admin-protected', fn () => response('ok'));

        $user = User::factory()->create();

        Admin::query()->create([
            'user_id' => $user->id,
            'status' => ActiveInactive::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/test-active-admin-protected')
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_address_can_belong_to_user_or_admin(): void
    {
        $user = User::factory()->create();

        $adminUser = User::factory()->create();
        $admin = Admin::query()->create([
            'user_id' => $adminUser->id,
            'status' => ActiveInactive::ACTIVE->value,
        ]);

        $userAddress = $user->addresses()->create([
            'label' => 'Home',
            'line_1' => 'Main Street 1',
            'city' => 'Dhaka',
            'country' => 'BD',
            'is_primary' => true,
        ]);

        $adminAddress = $admin->addresses()->create([
            'label' => 'Office',
            'line_1' => 'Admin Avenue 2',
            'city' => 'Dhaka',
            'country' => 'BD',
            'is_primary' => true,
        ]);

        $this->assertTrue($userAddress->addressable->is($user));
        $this->assertTrue($adminAddress->addressable->is($admin));
        $this->assertSame(2, Address::query()->count());
    }
}
