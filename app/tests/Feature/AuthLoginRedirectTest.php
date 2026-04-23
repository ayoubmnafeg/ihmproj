<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_is_redirected_to_admin_dashboard_on_login(): void
    {
        $user = User::factory()->create([
            'password' => 'secret12345',
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'admin',
        ]);

        DB::table('admins')->insert([
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
