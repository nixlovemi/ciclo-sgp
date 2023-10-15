<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'create_user_id' => function() {
                return User::whereIn('role', [User::ROLE_ADMIN])
                    ->where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'client_id' => function() {
                return Client::where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'validity_days' => $this->faker->numberBetween(1, 60),
            'payment_type' => $this->faker->randomElement(Quote::PAYMENT_TYPES),
            'payment_type_memo' => function() {
                return (rand(0, 1) === 0) ? null: $this->faker->text(150);
            },
            'notes' => function() {
                return (rand(0, 1) === 0) ? null: $this->faker->text(150);
            },
            'active' => $this->faker->randomElement([true, false]),
        ];
    }
}
