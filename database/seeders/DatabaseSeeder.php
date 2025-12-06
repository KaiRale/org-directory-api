<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $firstLevelActivities = Activity::factory()
            ->count(4)
            ->create();

        $firstLevelActivities->each(function (Activity $parentActivity) {
            $secondLevelActivities = Activity::factory()
                ->count(fake()->randomElement([0, 1, 2, 3]))
                ->create([
                    'parent_id' => $parentActivity->id
                ]);

            $secondLevelActivities->each(function (Activity $childActivity) {
                Activity::factory()
                    ->count(fake()->randomElement([0, 1, 2, 3]))
                    ->create([
                        'parent_id' => $childActivity->id
                    ]);
            });
        });

        Organization::factory()
            ->count(10)
            ->create();
    }
}
