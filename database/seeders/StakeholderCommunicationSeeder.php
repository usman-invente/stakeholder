<?php


namespace Database\Seeders;

use App\Models\Stakeholder;
use App\Models\StakeholderCommunication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StakeholderCommunicationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = User::pluck('id')->toArray(); // Get all users without role filter
        
        if (empty($users)) {
            // Create a default user if none exists
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
            $users = [$user->id];
        }

        $stakeholders = Stakeholder::pluck('id')->toArray();
        
        // Create 2-3 communications for each stakeholder
        foreach ($stakeholders as $stakeholderId) {
            $numCommunications = rand(2, 3);
            
            for ($i = 0; $i < $numCommunications; $i++) {
                $meetingDate = $faker->dateTimeBetween('-6 months', 'now');
                
                $communication = StakeholderCommunication::create([
                    'stakeholder_id' => $stakeholderId,
                    'meeting_date' => $meetingDate->format('Y-m-d'),
                    'meeting_time' => $faker->time('H:i'),
                    'meeting_type' => $faker->randomElement(['in-person', 'video', 'phone', 'email']),
                    'location' => $faker->randomElement(['Office', 'Virtual', 'Client Site', null]),
                    'attendees' => $faker->name . ', ' . $faker->name,
                    'discussion_points' => $faker->paragraph(3),
                    'action_items' => $faker->paragraph(2),
                    'follow_up_notes' => $faker->paragraph(1),
                    'follow_up_date' => $faker->dateTimeBetween($meetingDate, '+1 month')->format('Y-m-d'),
                    'user_id' => $faker->randomElement($users),
                ]);

                // Assign 1-3 random users to each communication
                $assignedUsers = $faker->randomElements(
                    $users,
                    min($faker->numberBetween(1, 3), count($users)),
                    false
                );
                $communication->users()->attach($assignedUsers);
            }
        }
    }
}