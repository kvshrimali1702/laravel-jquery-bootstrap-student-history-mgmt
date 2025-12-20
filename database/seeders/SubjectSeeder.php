<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Mathematics',
            'English',
            'Science',
            'Social Studies',
            'Hindi',
            'Physical Education',
            'Computer Science',
            'Art',
            'Music',
            'History',
            'Geography',
            'Physics',
            'Chemistry',
            'Biology',
            'Economics',
        ];

        foreach ($subjects as $subjectName) {
            Subject::firstOrCreate(
                ['name' => $subjectName],
                ['name' => $subjectName]
            );
        }
    }
}
