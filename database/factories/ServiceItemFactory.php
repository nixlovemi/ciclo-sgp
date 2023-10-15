<?php

namespace Database\Factories;

use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->text(80),
            'price' => function() {
                return (float) rand(1, 100) . '.' . rand(0, 99);
            },
            'currency' => $this->faker->randomElement(ServiceItem::CURRENCY_TYPES),
            'active' => $this->faker->randomElement([true, false]),
        ];
    }
}
