<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uid' => 'PIT ' . $this->faker->unique()->numberBetween(1001, 8000),
            'create_user_id' => function() {
                return User::whereIn('role', [User::ROLE_ADMIN])
                    ->where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'responsible_id' => function() {
                return User::where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'client_id' => function() {
                return Client::where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'title' => $this->faker->randomElement([
                'Rótulo Linha Desinfetante',
                'Banner Fachada do Prédio',
                'Website Ponto de Venda',
            ]),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'status' => $this->faker->randomElement(array_keys(Job::JOB_STATUSES))
        ];
    }
}
