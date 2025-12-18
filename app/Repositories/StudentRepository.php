<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    public function __construct(
        private readonly Student $model
    ) {}

    /**
     * Get all students with their addresses.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllWithAddresses()
    {
        return $this->model->with('address')->select('students.*');
    }
}

