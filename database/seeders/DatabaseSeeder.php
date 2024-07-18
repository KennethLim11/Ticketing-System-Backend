<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {   
        User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'John',
            'middle_name' => 'Doe',
            'last_name' => 'Wick',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'projects' => ['HR'],
            'mobile_number' => '123456789',
            'birthday' => '2024-7-7',
        ]);

        Ticket::factory(35)->create();

        Admin::factory(10)->create();

        Admin::factory()->create([
            'first_name' => 'Luka',
            'middle_name' => 'D.',
            'last_name' => 'Doncic',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role' => 'super_admin',
            'mobile_number' => '123456789',
            'birthday' => '2024-7-7',
        ]);
        
    }

}
