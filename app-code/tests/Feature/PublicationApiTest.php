<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PublicationApiTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $email = 'author@example.com'): User
    {
        $user = User::query()->create([
            'email' => $email,
            'password' => 'password123',
            'status' => 'active',
        ]);

        $user->profile()->create([
            'display_name' => str($email)->before('@')->value(),
        ]);

        return $user;
    }

    private function createPublication(User $author, array $overrides = []): Publication
    {
        $content = Content::query()->create([
            'type' => 'publication',
            'status' => $overrides['status'] ?? 'visible',
            'author_id' => $author->id,
        ]);

        DB::table('publications')->insert([
            'id' => $content->id,
            'title' => $overrides['title'] ?? 'Test publication',
            'text' => $overrides['text'] ?? 'Body copy',
            'media_type' => $overrides['media_type'] ?? null,
            'category_id' => $overrides['category_id'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Publication::query()->findOrFail($content->id);
    }

    public function test_guest_can_list_visible_publications(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);

        $response = $this->getJson('/api/publications');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $publication->id)
            ->assertJsonPath('data.0.title', 'Test publication');
    }

    public function test_guest_can_view_single_visible_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author, [
            'title' => 'Visible post',
        ]);

        $response = $this->getJson("/api/publications/{$publication->id}");

        $response->assertOk()
            ->assertJsonPath('id', $publication->id)
            ->assertJsonPath('title', 'Visible post');
    }

    public function test_guest_cannot_create_a_publication(): void
    {
        $response = $this->postJson('/api/publications', [
            'title' => 'Guest title',
            'text' => 'Guest body',
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_update_a_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);

        $response = $this->patchJson("/api/publications/{$publication->id}", [
            'title' => 'Updated by guest',
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_delete_a_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);

        $response = $this->deleteJson("/api/publications/{$publication->id}");

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_publication(): void
    {
        $author = $this->createUser();
        Sanctum::actingAs($author);

        $response = $this->postJson('/api/publications', [
            'title' => 'Created title',
            'text' => 'Created body',
        ]);

        $response->assertCreated()
            ->assertJsonPath('title', 'Created title')
            ->assertJsonPath('author_id', $author->id);
    }

    public function test_author_can_update_their_own_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);
        Sanctum::actingAs($author);

        $response = $this->patchJson("/api/publications/{$publication->id}", [
            'title' => 'Revised title',
        ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Revised title');
    }

    public function test_non_author_cannot_update_someone_elses_publication(): void
    {
        $author = $this->createUser();
        $intruder = $this->createUser('intruder@example.com');
        $publication = $this->createPublication($author);
        Sanctum::actingAs($intruder);

        $response = $this->patchJson("/api/publications/{$publication->id}", [
            'title' => 'Intruder title',
        ]);

        $response->assertForbidden();
    }

    public function test_author_can_delete_their_own_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);
        Sanctum::actingAs($author);

        $response = $this->deleteJson("/api/publications/{$publication->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Publication deleted.');

        $this->assertDatabaseHas('contents', [
            'id' => $publication->id,
            'status' => 'deleted',
        ]);
    }

    public function test_non_author_cannot_delete_someone_elses_publication(): void
    {
        $author = $this->createUser();
        $intruder = $this->createUser('intruder@example.com');
        $publication = $this->createPublication($author);
        Sanctum::actingAs($intruder);

        $response = $this->deleteJson("/api/publications/{$publication->id}");

        $response->assertForbidden();
    }
}
