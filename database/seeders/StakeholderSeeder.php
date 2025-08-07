<?php


namespace Database\Seeders;

use App\Models\Stakeholder;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StakeholderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        for ($i = 0; $i < 20; $i++) {
            Stakeholder::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'organization' => $faker->company,
                'position' => $faker->jobTitle,
                'address' => $faker->address,
                'type' => $faker->randomElement(['internal', 'external']),
                'notes' => $faker->paragraph(2),
            ]);
        }
    }
}