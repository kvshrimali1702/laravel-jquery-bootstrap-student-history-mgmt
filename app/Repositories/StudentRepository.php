<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class StudentRepository implements StudentRepositoryInterface
{
    public function __construct(
        private readonly Student $model
    ) {}

    /**
     * Get all students with their addresses.
     */
    public function getAllWithAddresses(): Builder
    {
        return $this->model->with('address')->select('students.*');
    }

    /**
     * Create a new student.
     */
    public function create(array $data): Student
    {
        return $this->model->create($data);
    }

    /**
     * Find a student by ID with optional relations.
     */
    public function find(int $id, array $relations = []): ?Student
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Update a student.
     */
    public function update(Student $student, array $data): bool
    {
        return $student->update($data);
    }

    /**
     * Delete a student.
     */
    public function delete(Student $student): bool
    {
        return $student->delete();
    }
}
