<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Apple Inc.',
            'Dell Technologies',
            'HP Inc.',
            'Lenovo',
            'Microsoft Corporation',
            'Amazon Web Services',
            'Cisco Systems',
            'Adobe Systems',
            'Oracle Corporation',
            'IBM',
            'Google LLC',
            'Samsung Electronics'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'contact_person' => $this->faker->name(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'website' => $this->faker->url(),
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}