<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Abdurahman',
            'email' => 'admin@gmail.com',
            'password' => '091091',
            'account_type' => 'admin',
        ]);


        $this->call(ShieldSeeder::class);

        $user = User::where('email', 'admin@gmail.com')->first();
        if ($user) {
            $user->assignRole('super_admin');
        }
    }
}
