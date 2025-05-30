<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Users JSON betöltése
        $usersJson = File::get(database_path('seeders/data/users.json'));
        $users = json_decode($usersJson, true);

        // Users létrehozása
        foreach ($users as $userData) {
            User::create($userData);
        }

        // Posts JSON betöltése
        $postsJson = File::get(database_path('seeders/data/posts.json'));
        $posts = json_decode($postsJson, true);

        // Posts létrehozása
        foreach ($posts as $postData) {
            // User ID megkeresése email alapján
            $user = User::where('email', $postData['user_email'])->first();

            if ($user) {
                // user_email eltávolítása és user_id hozzáadása
                unset($postData['user_email']);
                $postData['user_id'] = $user->id;

                Post::create($postData);
            }
        }
    }
}
