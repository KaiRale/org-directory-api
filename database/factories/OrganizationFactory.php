<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->company(),
            'building_id' => Building::factory(),
        ];
    }

    public function configure(): Factory|OrganizationFactory
    {
        return $this->afterCreating(function (Organization $organization) {
            $phoneCount = fake()->randomElement([1, 2, 3]);

            OrganizationPhone::factory()
                ->count($phoneCount)
                ->for($organization)
                ->create();

            $activities = Activity::inRandomOrder()->get();

            $maxToAttach = min(4, $activities->count());
            $activitiesToAttach = $activities->random(rand(1, $maxToAttach));

            $organization->activities()->attach($activitiesToAttach);
        });
    }
}
