<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Laptops',
            'Desktops',
            'Monitors',
            'Printers',
            'Mobile Devices',
            'Networking Equipment',
            'Servers',
            'Software',
            'Furniture',
            'Vehicles',
            'Tools',
            'Office Equipment'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
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