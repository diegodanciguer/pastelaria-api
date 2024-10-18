<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->streetAddress,
            'address_line2' => $this->faker->optional()->secondaryAddress,
            'neighborhood' => $this->faker->word,
            'postal_code' => $this->faker->postcode,
        ];
    }
}
