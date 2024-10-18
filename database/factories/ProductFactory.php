<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => ucfirst($this->faker->word) . ' ' . ucfirst($this->faker->word),
            'price' => $this->faker->randomFloat(2, 1, 50),
            'image' => 'images/' . $this->faker->image('public/images', 640, 480, null, false),
        ];
    }
}
