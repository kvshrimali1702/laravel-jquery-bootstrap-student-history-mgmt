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
}
