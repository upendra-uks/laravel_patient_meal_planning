<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PatientMealPlanningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();
        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->subYears(2);

        // Assuming patient table with ids ranging from 1 to 100
        $patientIds = range(1, 100);

        $records = [];

        for ($i = 0; $i < 500; $i++) {
            $patientId = $faker->randomElement($patientIds);
            $plannedDate = $faker->dateTimeBetween($startDate, $currentDate)->format('Y-m-d');
             // Prevent duplicate patient_id and planned_date combinations
             if (DB::table('patient_meal_planning')->where('patient_id', $patientId)->where('planned_date', $plannedDate)->exists()) {
                continue;
            }

        $records[] = [
            'patient_id' => $patientId,
            'planned_date' => $plannedDate,
            'total_calories' => $faker->randomFloat(2, 1000, 3000),
            'total_fats' => $faker->randomFloat(2, 20, 150),
            'total_carbs' => $faker->randomFloat(2, 100, 400),
            'total_proteins' => $faker->randomFloat(2, 50, 200),
            'is_active' => $faker->boolean(80),
            'created_by' => $faker->numberBetween(1, 10),
            'created_at' => $faker->dateTimeBetween($startDate, $currentDate),
            'updated_by' => $faker->numberBetween(1, 10),
            'updated_at' => $faker->dateTimeBetween($startDate, $currentDate),
        ];
    }

    // Insert the records into the database
    DB::table('patient_meal_planning')->insert($records);
    }
}
