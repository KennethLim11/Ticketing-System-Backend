<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        static $number = 1; 
        $admin_number = $number;
        $email = 'admin' . $number++ . '@gmail.com';
        return [
            'admin_number' => $admin_number,
            'email' => $email,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'role' => $this->faker->randomElement(['super_admin', 'admin', 'staff']),
            'mobile_number' => $this->faker->phoneNumber(),
            'birthday' => $this->faker->date(),
            'password' => 'password',
            'remember_token' => Str::random(10),
        ];
    }
}

