<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Product::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'product_name' => $this->faker->word(),
            'brand_name' => $this->faker->word(),
            'description' => $this->faker->text(50),
            'condition' => '良好',
            'price' => 1000,
            'status' => 'selling',
        ];
    }
}
