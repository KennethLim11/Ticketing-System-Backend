<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        static $number = 1; 
        $client_number = $number++;
        $email = $this->faker->firstName . '@gmail.com';

        $possibleProjects = ['PPOAS', 'PARPS', 'LBCC', 'TALKENCOURAGE', 'ASCENDS', 'LABSONGS', 'HR', 'SMMS', 'AUTOMATE'];
        
        $projects = $this->faker->randomElements($possibleProjects, $this->faker->numberBetween(1, 3));
    
        return [
            'client_number' => $client_number,
            'email' => $email,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'mobile_number' => $this->faker->phoneNumber(),
            'birthday' => $this->faker->date(),
            'projects' => $projects,
            'password' => 'password',
            'remember_token' => Str::random(10),
        ];
    }
}
