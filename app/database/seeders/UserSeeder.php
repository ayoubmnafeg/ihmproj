<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Database\Factories\CommentFactory;
use Database\Factories\PublicationFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 100 users with profiles
        $users = User::factory(100)->create();

        foreach ($users as $user) {
            Profile::factory()->create(['user_id' => $user->id]);
        }

        $pubFactory     = new PublicationFactory();
        $commentFactory = new CommentFactory();
        $now            = now();

        // 3-7 publications per user
        $publicationIds = [];
        foreach ($users as $user) {
            $count = rand(3, 10);
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

        // 2-5 comments per publication from random users
        foreach ($publicationIds as $pubId) {
            $count = rand(2, 100);
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
    }
}
