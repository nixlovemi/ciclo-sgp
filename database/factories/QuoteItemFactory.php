<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quote_id' => function() {
                return Quote::inRandomOrder()
                    ->first();
            },
            'item_id' => function() {
                return ServiceItem::where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'quantity' => $this->faker->numberBetween(1, 7),
            'type' => $this->faker->randomElement(['UN', 'Hrs', 'PÇ']),
            'price' => function() {
                return (float) rand(11, 100) . '.' . rand(0, 99);
            },
            'discount' => function() {
                return rand(0, 1) ? (float) rand(1, 10) . '.' . rand(0, 99) : null;
            },
            'total' => function(array $attributes) {
                try {
                    return $attributes['price'] * $attributes['quantity'];
                } catch (\Throwable $th) {
                    return 0;
                }
            },
        ];
    }
}
