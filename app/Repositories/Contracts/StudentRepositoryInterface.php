<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface
{
    /**
     * Get all students with their addresses.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllWithAddresses();
}

