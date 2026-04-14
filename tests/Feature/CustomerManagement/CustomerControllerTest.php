<?php

namespace Tests\Feature\CustomerManagement;

use App\Enums\ActiveInactive;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_customer_index_page(): void
    {
        $adminUser = User::factory()->create();

        Admin::query()->create([
            'user_id' => $adminUser->id,
            'status' => ActiveInactive::ACTIVE->value,
        ]);

        $this->actingAs($adminUser)
            ->get(route('admin.customer.index'))
            ->assertOk();
    }
}
