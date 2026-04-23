<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Database\Factories\CommentFactory;
use Database\Factories\PublicationFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Smaller dataset for faster local seeding.
        $users = User::factory(24)->create([
            'password' => Hash::make('123456789'),
        ]);

        foreach ($users as $user) {
            Profile::factory()->create(['user_id' => $user->id]);
        }

        // Seed a mix of accepted friendships and pending friend requests.
        $friendPairs = [];
        $friendRequestRows = [];

        foreach ($users as $user) {
            $friendCandidates = $users
                ->where('id', '!=', $user->id)
                ->shuffle()
                ->take(rand(4, 10));

            foreach ($friendCandidates as $friend) {
                $pair = [$user->id, $friend->id];
                $normalizedPair = $pair;
                sort($normalizedPair);
                $pairKey = implode('|', $normalizedPair);

                if (isset($friendPairs[$pairKey])) {
                    continue;
                }

                $friendPairs[$pairKey] = true;
                $status = rand(1, 100) <= 65 ? 'accepted' : 'pending';

                $friendRequestRows[] = [
                    'id' => Str::uuid()->toString(),
                    'sender_id' => $pair[0],
                    'receiver_id' => $pair[1],
                    'status' => $status,
                    'responded_at' => $status === 'accepted' ? $now : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($friendRequestRows)) {
            DB::table('friend_requests')->insert($friendRequestRows);
        }

        $pubFactory     = new PublicationFactory();
        $commentFactory = new CommentFactory();

        // 2-4 publications per user
        $publicationIds = [];
        foreach ($users as $user) {
            $count = rand(2, 4);
            for ($i = 0; $i < $count; $i++) {
                $id         = Str::uuid()->toString();
                $attrs      = $pubFactory->definition();

                DB::table('contents')->insert([
                    'id'         => $id,
                    'type'       => 'publication',
                    'status'     => 'visible',
                    'author_id'  => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('publications')->insert([
                    'id'         => $id,
                    'title'      => $attrs['title'],
                    'text'       => $attrs['text'],
                    'media_type' => $attrs['media_type'],
                    'category_id'=> null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $publicationIds[] = $id;
            }
        }

        // 1-8 comments per publication from random users
        foreach ($publicationIds as $pubId) {
            $count = rand(1, 8);
            for ($i = 0; $i < $count; $i++) {
                $id    = Str::uuid()->toString();
                $attrs = $commentFactory->definition();

                DB::table('contents')->insert([
                    'id'         => $id,
                    'type'       => 'comment',
                    'status'     => 'visible',
                    'author_id'  => $users->random()->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('comments')->insert([
                    'id'             => $id,
                    'text'           => $attrs['text'],
                    'publication_id' => $pubId,
                    'parent_id'      => null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        }

        if ($this->command) {
            $this->command->newLine();
            $this->command->info('Seeded usernames (use password: 123456789):');

            foreach ($users->take(4) as $sampleUser) {
                $this->command->line('- ' . ($sampleUser->profile->display_name ?? 'unknown_user'));
            }
        }
    }
}
