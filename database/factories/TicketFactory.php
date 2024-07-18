<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    private static $ticketNumber = 1;

    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $projects = $user->projects;
        $project = $this->faker->randomElement($projects);

        return [
            'reported_date' => $this->faker->date(),
            'type' => $this->faker->randomElement(['System Issue', 'User-related Issue', 'Others']),
            'type_other' => $this->faker->optional()->sentence(),
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['Open', 'On-going', 'Closed']),
            'project' => $project,
            'file_path_url' => null,
            'ticketable_id' => $user->id,
            'ticketable_type' => User::class,
            'ticket_number' => self::$ticketNumber++,
        ];
    }
}
