<?php

namespace App\Services;

use App\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectService
{
    public function __construct(
        private readonly SubjectRepositoryInterface $subjectRepository
    ) {}

    /**
     * Get all subjects.
     */
    public function getAll(): Collection
    {
        return $this->subjectRepository->getAll();
    }

    /**
     * Search subjects by name.
     */
    public function searchByName(string $search): Collection
    {
        return $this->subjectRepository->searchByName($search);
    }
}
