<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Storekeeper;

class StorekeeperFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Storekeeper::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'document' => rand(111111111, 999999999),
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password_storekeeper')
        ];
    }

}
