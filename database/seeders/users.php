<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Jesus Ivan',
            'email' => 'jchugut@gmail.com',
            'password' => Hash::make('zcrat123')
            ]);
    }
}
