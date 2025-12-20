<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SubjectRepositoryInterface
{
    /**
     * Get all subjects.
     */
    public function getAll(): Collection;

    /**
     * Search subjects by name.
     */
    public function searchByName(string $search): Collection;
}
