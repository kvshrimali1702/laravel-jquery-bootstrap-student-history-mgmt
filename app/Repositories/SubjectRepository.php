<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function __construct(
        private readonly Subject $model
    ) {}

    /**
     * Get all subjects.
     */
    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Search subjects by name.
     */
    public function searchByName(string $search): Collection
    {
        return $this->model->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->get();
    }
}
