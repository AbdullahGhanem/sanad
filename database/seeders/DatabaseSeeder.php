<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sanad.test',
        ]);

        User::factory()->universityAdmin()->create([
            'name' => 'University Admin',
            'email' => 'university@sanad.test',
            'faculty_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Test Student',
            'email' => 'student@sanad.test',
        ]);

        $this->call([
            Phq9Seeder::class,
            Gad7Seeder::class,
            CrisisKeywordSeeder::class,
            RecommendationSeeder::class,
        ]);
    }
}
