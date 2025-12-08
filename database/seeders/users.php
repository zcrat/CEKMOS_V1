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
        $yo=User::create([
            'name' => 'Jesus Ivan Gutierrez Villaseñor',
            'usuario'=>'zcrat',
            'email' => 'jchugut@gmail.com',
            'password' => Hash::make('zcrat123')
        ]);
        $odi=User::create([
            'name' => 'Odilon Rodriguez Gutierrez',
            'usuario' => 'admin',
            'email' => 'Akumas@gmail.com',
            'password' => Hash::make('admin')
        ]);
        $foster=User::create([
            'name' => 'J. Guadalupe Rodriguez Gutierrez',
            'usuario' => 'foster',
            'email' => 'foster@gmail.com',
            'password' => Hash::make('foster')
        ]);

        $yo->assignRole('Super Admin');
        $odi->assignRole('Super Admin');
        $foster->assignRole('Administrador 1');
    }
}
