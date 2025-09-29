<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Finance Department', 'description' => 'Handles financial operations and budgeting'],
            ['name' => 'Technical Department', 'description' => 'Manages technical operations and maintenance'],
            ['name' => 'Procurement Department', 'description' => 'Responsible for purchasing and supplier management'],
            ['name' => 'Logistics Department', 'description' => 'Manages transportation and supply chain'],
            ['name' => 'OHSSEQ Department', 'description' => 'Occupational Health, Safety, Security, Environment & Quality'],
            ['name' => 'Sales & Marketing Department', 'description' => 'Handles sales activities and marketing campaigns'],
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }
}
