<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_page(): void
    {
        $admin = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $admin->id,
            'display_name' => 'admin_user',
        ]);

        DB::table('admins')->insert([
            'user_id' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin Dashboard');
    }

    public function test_non_admin_cannot_access_dashboard_page(): void
    {
        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'normal_user',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_visiting_root_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $admin->id,
            'display_name' => 'root_admin',
        ]);

        DB::table('admins')->insert([
            'user_id' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('feed.index'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
