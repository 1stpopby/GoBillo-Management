<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Main Office',
            'Warehouse A',
            'Warehouse B',
            'Remote Office',
            'Branch Office',
            'Data Center',
            'Manufacturing Floor',
            'Executive Floor',
            'IT Department',
            'HR Department'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zip_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
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