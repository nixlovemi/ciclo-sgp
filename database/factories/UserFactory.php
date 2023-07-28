<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UserFactory extends Factory
{
    private const PATH_USER_IMAGES = '/img/demo/users/';
    private array $userImages = [
        self::PATH_USER_IMAGES . 'user-1.jpg',
        self::PATH_USER_IMAGES . 'user-2.jpg',
        self::PATH_USER_IMAGES . 'user-4.jpg',
        self::PATH_USER_IMAGES . 'user-5.jpg',
        self::PATH_USER_IMAGES . 'user-6.jpg',
        self::PATH_USER_IMAGES . 'user-7.jpg',
        self::PATH_USER_IMAGES . 'user-8.jpg',
        self::PATH_USER_IMAGES . 'user-9.jpg',
        self::PATH_USER_IMAGES . 'user-3.jpg',
        self::PATH_USER_IMAGES . 'user-10.jpg',
        self::PATH_USER_IMAGES . 'user-11.jpg',
        self::PATH_USER_IMAGES . 'user-12.jpg',
        self::PATH_USER_IMAGES . 'user-13.jpg',
        self::PATH_USER_IMAGES . 'user-14.jpg',
        self::PATH_USER_IMAGES . 'user-15.jpg',
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'picture_url' => $this->faker->randomElement($this->userImages),
            'password' => User::fPasswordHash('Mudar123'),
            'password_reset_token' => null,
            'role' => $this->faker->randomElement(array_keys(User::USER_ROLES)),
            'active' => $this->faker->randomElement([true, false]),
        ];
    }
}
