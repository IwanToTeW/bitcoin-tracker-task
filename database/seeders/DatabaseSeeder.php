<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Ivan',
            'last_name' => 'Totev',
            'password' => bcrypt('password'),
            'email' => 'ivan.totev@ampeco.com',
        ]);

        User::factory()->create([
            'first_name' => 'Maksim',
            'last_name' => 'Atanasov',
            'password' => bcrypt('password'),
            'email' => 'maksim.atanasov@ampeco.com',
        ]);
    }
}
