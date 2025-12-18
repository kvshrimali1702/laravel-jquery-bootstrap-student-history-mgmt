<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::factory(50)->create();

        foreach ($students as $student) {
            Address::factory()->create([
                'student_id' => $student->id,
            ]);
        }
    }
}
