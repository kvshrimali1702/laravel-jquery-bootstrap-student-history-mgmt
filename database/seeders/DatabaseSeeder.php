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

        User::factory()->create([
            'name' => 'Krunal Shrimali',
            'email' => 'krunal.shrimali@yopmail.com',
        ]);

        // Required data for the application to work
        $this->call([
            SubjectSeeder::class,
        ]);

        // Optional data for the application to work
        // $this->call([
        //     StudentSeeder::class,
        // ]);
    }
}
