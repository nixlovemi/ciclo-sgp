<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->UserSeed();
        $this->ClientSeed();
    }

    private function UserSeed(): void
    {
        foreach(array_keys(User::USER_ROLES) as $role) {
            foreach([true, false] as $active) {
                User::factory(1)->create([
                    'role' => $role,
                    'active' => $active,
                ]);
            }
        }
    }

    private function ClientSeed(): void
    {
        Client::factory(10)->create();
    }
}
