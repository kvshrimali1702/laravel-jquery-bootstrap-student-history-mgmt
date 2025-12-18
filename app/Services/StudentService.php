<?php

namespace App\Services;

use App\Enums\StudentStatus;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudentService
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository
    ) {}

    /**
     * Get students data for DataTables.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataTableData(Request $request)
    {
        $students = $this->studentRepository->getAllWithAddresses();

        return DataTables::of($students)
            ->addColumn('profile_picture', function ($student) {
                return $this->formatProfilePicture($student);
            })
            ->editColumn('first_name', function ($student) {
                return e($student->first_name);
            })
            ->editColumn('last_name', function ($student) {
                return e($student->last_name);
            })
            ->editColumn('birth_date', function ($student) {
                return $this->formatBirthDate($student);
            })
            ->addColumn('status', function ($student) {
                return $this->formatStatus($student);
            })
            ->addColumn('address', function ($student) {
                return $this->formatAddress($student);
            })
            ->addColumn('actions', function ($student) {
                return '';
            })
            ->rawColumns(['profile_picture', 'status', 'address', 'actions'])
            ->make(true);
    }

    /**
     * Format profile picture column.
     */
    private function formatProfilePicture($student): string
    {
        if ($student->profile_picture) {
            return '<img src="'.e($student->profile_picture).'" alt="Profile" class="rounded-circle" width="40" height="40">';
        }

        $initial = strtoupper(substr($student->first_name, 0, 1));

        return '<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 14px;">'.$initial.'</div>';
    }

    /**
     * Format birth date column.
     */
    private function formatBirthDate($student): string
    {
        return $student->birth_date ? $student->birth_date->format('Y-m-d') : '';
    }

    /**
     * Format status column.
     */
    private function formatStatus($student): string
    {
        $status = $student->status;
        $badgeClass = $status === StudentStatus::Active
            ? 'bg-success'
            : 'bg-danger';

        return '<span class="badge '.$badgeClass.'">'.e($status->label()).'</span>';
    }

    /**
     * Format address column.
     */
    private function formatAddress($student): string
    {
        if ($student->address) {
            return e($student->address->full_address);
        }

        return '<span class="text-muted">No address</span>';
    }
}

