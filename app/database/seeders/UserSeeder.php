<?php

namespace Database\Seeders;

use App\Models\Admin;
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

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@ihm.local'],
            [
                'password' => Hash::make('admin123456'),
                'status' => 'active',
            ]
        );

        Profile::query()->firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'display_name' => 'admin',
                'gender' => null,
            ]
        );

        Admin::query()->firstOrCreate(['user_id' => $adminUser->id]);

        // Seed 200 categories with Tunisia-related themes.
        $topicBuckets = [
            'people' => ['Jeunes Tunis', 'Diaspora TN', 'Voix de Sfax', 'Communautes de Tunis', 'Familles du Sahel'],
            'tech' => ['Startup Tunisia', 'DevOps TN', 'AI Tunisia', 'CyberSec Maghreb', 'NoCode Tunis'],
            'politics' => ['Debat Citoyen', 'Politiques Publiques TN', 'Elections & Reformes', 'Parlement Watch', 'Gouvernance Locale'],
            'nutrition' => ['Nutrition Tunisienne', 'Recettes Saines TN', 'Diet Mediteraneen', 'Sport & Nutrition', 'Nutrition Parents'],
            'islam' => ['Fiqh au Quotidien', 'Tafsir Circle', 'Jeunesse & Islam', 'Valeurs Islamiques', 'Sira & Histoire'],
        ];
        $suffixes = ['Hub', 'Network', 'Community', 'Forum', 'Club', 'Collective', 'Lab', 'Circle'];
        $descriptionsByTheme = [
            'people' => 'Espace communautaire tunisien pour partager initiatives, entraide et actualites locales.',
            'tech' => 'Discussions tech en Tunisie: dev, IA, securite, startups et opportunites.',
            'politics' => 'Echanges respectueux autour de la politique tunisienne, institutions et citoyennete.',
            'nutrition' => 'Conseils nutritionnels inspires des habitudes tunisiennes et du style mediterraneen.',
            'islam' => 'Partage de ressources islamiques, rappels benefiques et apprentissage en commun.',
        ];

        $categoryRows = [];
        $categoryIds = [];
        $usedNames = [];
        for ($i = 1; $i <= 200; $i++) {
            $theme = array_rand($topicBuckets);
            $base = $topicBuckets[$theme][array_rand($topicBuckets[$theme])];
            $suffix = $suffixes[array_rand($suffixes)];
            $candidateName = $base . ' ' . $suffix;
            $name = $candidateName;
            $dedupe = 2;
            while (isset($usedNames[$name])) {
                $name = $candidateName . ' ' . $dedupe;
                $dedupe++;
            }
            $usedNames[$name] = true;

            $categoryId = Str::uuid()->toString();
            $categoryIds[] = $categoryId;
            $categoryRows[] = [
                'id' => $categoryId,
                'name' => $name,
                'description' => $descriptionsByTheme[$theme],
                'profile_image_path' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('categories')->insert($categoryRows);

        // Each user follows 4 categories.
        $categoryFollowerRows = [];
        foreach ($users as $user) {
            $followed = collect($categoryIds)->shuffle()->take(4);
            foreach ($followed as $categoryId) {
                $categoryFollowerRows[] = [
                    'category_id' => $categoryId,
                    'user_id' => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('category_followers')->insert($categoryFollowerRows);

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
                    'category_id'=> collect($categoryIds)->random(),
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

            $this->command->newLine();
            $this->command->info('Admin login: admin / admin123456');
        }
    }
}
