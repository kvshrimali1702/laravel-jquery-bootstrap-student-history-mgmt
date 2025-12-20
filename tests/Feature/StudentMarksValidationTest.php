<?php

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('accepts obtained_marks of 0', function () {
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
                'obtained_marks' => 0,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('students.store'), $payload)
        ->assertCreated()
        ->assertJsonPath('success', true);
});

it('rejects negative obtained_marks', function () {
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
                'obtained_marks' => -1,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('students.store'), $payload)
        ->assertUnprocessable()
        ->assertInvalid(['marks.0.obtained_marks']);
});

it('rejects obtained_marks greater than total_marks', function () {
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
                'obtained_marks' => 150,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('students.store'), $payload)
        ->assertUnprocessable()
        ->assertInvalid(['marks.0.obtained_marks']);
});
