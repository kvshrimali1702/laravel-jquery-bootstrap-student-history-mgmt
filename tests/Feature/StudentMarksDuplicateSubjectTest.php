<?php

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects duplicate subject_id entries within marks for a student', function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    $user = User::factory()->create();
    $subject = Subject::create(['name' => 'Mathematics']);

    $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_date' => '2000-01-01',
        'standard' => 5,
        'status' => 1,
        'full_address' => '123 Main Street',
        'city' => 'Test City',
        'postcode' => '12345',
        'state' => 'Test State',
        'country' => 'Test Country',
        'marks' => [
            [
                'subject_id' => $subject->id,
                'total_marks' => 100,
                'obtained_marks' => 80,
            ],
            [
                'subject_id' => $subject->id,
                'total_marks' => 100,
                'obtained_marks' => 70,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('students.store'), $payload)
        ->assertUnprocessable()
        ->assertInvalid(['marks.1.subject_id']);
});
