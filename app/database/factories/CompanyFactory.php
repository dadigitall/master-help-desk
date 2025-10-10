<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->sentence(10),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'postal_code' => $this->faker->postcode(),
            'website' => $this->faker->url(),
            'industry' => $this->faker->randomElement(['Technology', 'Healthcare', 'Finance', 'Education', 'Retail']),
            'size' => $this->faker->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'is_active' => true,
            'icon_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the company is a technology company.
     */
    public function technology(): static
    {
        return $this->state(fn (array $attributes) => [
            'industry' => 'Technology',
            'website' => 'https://' . $this->faker->domainName() . '.tech',
        ]);
    }

    /**
     * Indicate that the company is large.
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $this->faker->randomElement(['201-500', '500+']),
        ]);
    }
}
