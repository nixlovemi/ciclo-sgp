<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Avlima\PhpCpfCnpjGenerator\Generator;
use App\Models\User;
use App\Helpers\Country;

class ClientFactory extends Factory
{
    private const ADDRESSES = [
        '13477-708' => [
            'street' => 'R. Progresso, 317',
            'street_2' => 'Jardim Boer',
            'city' => 'Americana',
            'province' => 'SP',
            'country' => Country::C_BRASIL
        ],
        '13380-374' => [
            'street' => 'Av. Carlos Rosenfeld, 185',
            'street_2' => 'Parque Industrial Recanto',
            'city' => 'Nova Odessa',
            'province' => 'SP',
            'country' => Country::C_BRASIL
        ],
        '10017' => [
            'street' => '45 Grand Central Terminal',
            'street_2' => '',
            'city' => 'Nova Iorque',
            'province' => 'NY',
            'country' => Country::C_USA
        ],
    ];
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'create_user_id' => function() {
                return User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER])
                    ->where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'business_name' => $this->faker->company(),
            'business_id' => function() {
                $rand = rand(1, 3);
                switch ($rand) {
                    case 1:
                        // CPF
                        return Generator::cpf(true);
                        break;

                    case 2:
                        // CNPJ
                        return Generator::cnpj(true);
                        break;
                    
                    default:
                        // ANYTHING ELSE
                        return $this->faker->uuid();
                        break;
                }
            },
            'business_email' => $this->faker->unique()->companyEmail(),
            'business_phone' => $this->faker->phoneNumber(),
            'postal_code' => $this->faker->randomElement(array_keys(ClientFactory::ADDRESSES)),
            'street' => function(array $attributes) {
                return ClientFactory::ADDRESSES[$attributes['postal_code']]['street'];
            },
            'street_2' => function(array $attributes) {
                return ClientFactory::ADDRESSES[$attributes['postal_code']]['street_2'];
            },
            'city' => function(array $attributes) {
                return ClientFactory::ADDRESSES[$attributes['postal_code']]['city'];
            },
            'province' => function(array $attributes) {
                return ClientFactory::ADDRESSES[$attributes['postal_code']]['province'];
            },
            'country' => function(array $attributes) {
                return ClientFactory::ADDRESSES[$attributes['postal_code']]['country'];
            },
            'notes' => $this->faker->text(),
            'active' => $this->faker->randomElement([true, false]),
        ];
    }
}
