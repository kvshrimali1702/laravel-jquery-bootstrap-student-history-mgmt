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
}
