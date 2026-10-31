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

        $events = [
            [
                'organizer_id' => 1,
                'location_id' => 1,
                'title' => 'Randonnée au Creux du Van',
                'description' => 'Une randonnée conviviale pour découvrir les paysages du Creux du Van.',
                'price' => 0,
                'max_participants' => 20,
                'min_age' => 16,
                'status' => 'open',
                'starts_at' => '2026-11-07 09:00:00',
                'ends_at' => '2026-11-07 16:00:00',
                'image_url' => null,
                'updated_at' => now(),
            ],
            [
                'organizer_id' => 1,
                'location_id' => 1,
                'title' => 'Atelier cuisine italienne',
                'description' => 'Apprenez à préparer des pâtes fraîches et une sauce maison.',
                'price' => 25,
                'max_participants' => 12,
                'min_age' => 18,
                'status' => 'open',
                'starts_at' => '2026-11-14 14:00:00',
                'ends_at' => '2026-11-14 17:00:00',
                'image_url' => null,
                'updated_at' => now(),
            ],
            [
                'organizer_id' => 1,
                'location_id' => 1,
                'title' => 'Soirée jeux de société',
                'description' => 'Une soirée conviviale autour de jeux de stratégie et de coopération.',
                'price' => 5,
                'max_participants' => 30,
                'min_age' => 12,
                'status' => 'open',
                'starts_at' => '2026-11-20 18:30:00',
                'ends_at' => '2026-11-20 22:00:00',
                'image_url' => null,
                'updated_at' => now(),
            ],
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

        foreach ($events as $event) {
            \App\Models\Event::create($event);
        }
    }
}