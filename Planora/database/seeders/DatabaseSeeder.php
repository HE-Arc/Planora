<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        \App\Models\Location::truncate();
        \App\Models\Role::truncate();
        \App\Models\User::truncate();
        \App\Models\Tag::truncate();

        $locations = [
            ['name' => 'he-arc', 'address' => 'Espace de l\'Europe 11, 2000 Neuchâtel'],
            ['name' => 'Gare neuchatel', 'address' => 'Place de la Gare 1, 2000 Neuchâtel'],
        ];

        $roles = [
            ['name' => 'organizer'],
            ['name' => 'participant'],
        ];

        // to check password : password_verify($password, $user->password)
        $users = [
            ['email' => 'Alexis.Flury@he-arc.ch', 'password' => password_hash('pass', PASSWORD_DEFAULT), 'role_id' => 1],
            ['email' => 'Johan.Darioly@he-arc.ch', 'password' => password_hash('pass', PASSWORD_DEFAULT), 'role_id' => 1],
            ['email' => 'Sacha.Gruring@he-arc.ch', 'password' => password_hash('pass', PASSWORD_DEFAULT), 'role_id' => 1],
        ];

        $tags = [
            ['name' => 'sport'],
            ['name' => 'music'],
            ['name' => 'game'],
            ['name' => 'party'],
        ];

        foreach ($locations as $location) {
            \App\Models\Location::create(array(
                'name' => $location['name'],
                'address' => $location['address']   
            ));
        }

        foreach ($roles as $role) {
            \App\Models\Role::create(array( 
                'name' => $role['name']
            ));
        }

        foreach ($users as $user) {
            \App\Models\User::create(array(
                'email' => $user['email'],
                'password' => $user['password'],
                'role_id' => $user['role_id']
            ));
        }

        foreach ($tags as $tag) {
            \App\Models\Tag::create(array(
                'name' => $tag['name']
            ));
        }
    }
}