<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrganizationPhoneFactory extends Factory
{
    protected $model = OrganizationPhone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
