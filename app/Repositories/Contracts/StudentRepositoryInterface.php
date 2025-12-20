<?php

namespace App\Repositories\Contracts;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;

interface StudentRepositoryInterface
{
    /**
     * Get all students with their addresses.
     */
    public function getAllWithAddresses(): Builder;

    /**
     * Create a new student.
     */
    public function create(array $data): Student;

    /**
     * Find a student by ID with optional relations.
     */
    public function find(int $id, array $relations = []): ?Student;

    /**
     * Update a student.
     */
    public function update(Student $student, array $data): bool;

    /**
     * Delete a student.
     */
    public function delete(Student $student): bool;
}
