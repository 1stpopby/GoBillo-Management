<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $purchaseDate = $this->faker->dateTimeBetween('-5 years', 'now');
        $depreciationMethod = $this->faker->randomElement(['NONE', 'STRAIGHT_LINE']);
        
        return [
            'asset_code' => Asset::generateAssetCode(),
            'name' => $this->faker->randomElement([
                'MacBook Pro 14"',
                'Dell OptiPlex 7090',
                'HP EliteBook 850',
                'Lenovo ThinkPad X1',
                'iPad Pro 12.9"',
                'Samsung Monitor 27"',
                'Canon Printer MX490',
                'iPhone 14 Pro',
                'Microsoft Surface Pro',
                'ASUS ROG Laptop'
            ]),
            'description' => $this->faker->optional()->sentence(),
            'category_id' => AssetCategory::factory(),
            'location_id' => Location::factory(),
            'vendor_id' => Vendor::factory(),
            'purchase_date' => $purchaseDate,
            'purchase_cost' => $this->faker->randomFloat(2, 100, 5000),
            'depreciation_method' => $depreciationMethod,
            'depreciation_life_months' => $depreciationMethod === 'STRAIGHT_LINE' 
                ? $this->faker->numberBetween(12, 60) 
                : null,
            'status' => $this->faker->randomElement(['IN_STOCK', 'ASSIGNED', 'MAINTENANCE', 'RETIRED', 'LOST']),
            'assignee_id' => $this->faker->boolean(60) ? User::factory() : null,
            'department' => $this->faker->optional()->randomElement(['IT', 'HR', 'Finance', 'Marketing', 'Operations']),
            'serial_number' => $this->faker->optional()->bothify('??##########'),
            'warranty_expiry' => $this->faker->optional()->dateTimeBetween('now', '+3 years'),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    public function inStock(): static
    {
        return $this->state(fn () => [
            'status' => 'IN_STOCK',
            'assignee_id' => null,
            'department' => null,
        ]);
    }

    public function assigned(): static
    {
        return $this->state(fn () => [
            'status' => 'ASSIGNED',
            'assignee_id' => User::factory(),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Marketing', 'Operations']),
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn () => ['status' => 'MAINTENANCE']);
    }

    public function retired(): static
    {
        return $this->state(fn () => ['status' => 'RETIRED']);
    }

    public function withDepreciation(): static
    {
        return $this->state(fn () => [
            'depreciation_method' => 'STRAIGHT_LINE',
            'depreciation_life_months' => $this->faker->numberBetween(12, 60),
        ]);
    }

    public function withoutDepreciation(): static
    {
        return $this->state(fn () => [
            'depreciation_method' => 'NONE',
            'depreciation_life_months' => null,
        ]);
    }
}