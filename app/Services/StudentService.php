<?php

namespace App\Services;

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\StudentSubjectMark;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StudentService
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly FileUploadService $fileUploadService
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
                return view('students.partials.actions', compact('student'))->render();
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

    /**
     * Create a new student with address and marks.
     */
    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $disk = $this->fileUploadService->getTargetDisk();

            // Handle profile picture upload
            if (isset($data['profile_picture']) && $data['profile_picture']) {
                $data['profile_picture'] = $this->fileUploadService->upload(
                    $data['profile_picture'],
                    'students/profile-pictures',
                    $disk
                );
                $data['profile_picture_disk'] = $disk;
            }

            // Extract address data
            $addressData = $this->extractAddressData($data);

            // Extract marks data
            $marksData = $data['marks'] ?? [];

            // Clean student data - remove nested data that will be created separately
            $studentData = $this->extractStudentData($data);

            // Create student
            $student = $this->studentRepository->create($studentData);

            // Create address
            $student->address()->create($addressData);

            // Prepare marks for bulk insert
            $marksToInsert = $this->prepareMarksForInsert($marksData, $student->id, $disk);

            // Bulk insert marks for better performance
            if (! empty($marksToInsert)) {
                StudentSubjectMark::insert($marksToInsert);
            }

            return $student->load('address', 'studentSubjectMarks.subject');
        });
    }

    /**
     * Get student by ID with relations.
     */
    public function getStudentById(int $id): ?Student
    {
        return $this->studentRepository->find($id, ['address', 'studentSubjectMarks.subject']);
    }

    /**
     * Update an existing student.
     */
    public function updateStudent(int $id, array $data): Student
    {
        $student = $this->studentRepository->find($id);

        if (! $student) {
            throw new \Exception('Student not found.');
        }

        return DB::transaction(function () use ($student, $data) {
            $disk = $this->fileUploadService->getTargetDisk();

            // Handle profile picture update
            if (isset($data['profile_picture']) && $data['profile_picture']) {
                // Delete old profile picture if exists
                if ($student->profile_picture) {
                    $this->fileUploadService->delete($student->profile_picture, $student->profile_picture_disk);
                }

                $data['profile_picture'] = $this->fileUploadService->upload(
                    $data['profile_picture'],
                    'students/profile-pictures',
                    $disk
                );
                $data['profile_picture_disk'] = $disk;
            }

            // Extract nested data
            $addressData = $this->extractAddressData($data);
            $marksData = $data['marks'] ?? [];
            $studentData = $this->extractStudentData($data);

            // Update student
            $this->studentRepository->update($student, $studentData);

            // Update address
            $student->address()->update($addressData);

            // Update marks
            $this->updateStudentMarks($student, $marksData);

            return $student->load('address', 'studentSubjectMarks.subject');
        });
    }

    /**
     * Delete a student and associated data.
     */
    public function deleteStudent(int $id): bool
    {
        $student = $this->studentRepository->find($id, ['address', 'studentSubjectMarks']);

        if (! $student) {
            throw new \Exception('Student not found.');
        }

        return DB::transaction(function () use ($student) {
            // Delete profile picture
            if ($student->profile_picture) {
                $this->fileUploadService->delete($student->profile_picture, $student->profile_picture_disk);
            }

            // Delete proof files for marks
            foreach ($student->studentSubjectMarks as $mark) {
                if ($mark->proof) {
                    $this->fileUploadService->delete($mark->proof, $mark->proof_disk);
                }
            }

            // Delete marks (cascade should handle this if defined in migration, but we'll be explicit if needed)
            $student->studentSubjectMarks()->delete();

            // Delete address
            $student->address()->delete();

            // Delete student
            return $this->studentRepository->delete($student);
        });
    }

    /**
     * Update student marks.
     */
    private function updateStudentMarks(Student $student, array $marksData): void
    {
        $existingMarkIds = $student->studentSubjectMarks->pluck('id')->toArray();
        $newMarkIds = [];
        $disk = $this->fileUploadService->getTargetDisk();

        foreach ($marksData as $markData) {
            $markId = $markData['id'] ?? null;

            // Handle proof file upload
            if (isset($markData['proof']) && $markData['proof']) {
                // If updating existing mark, delete old proof
                if ($markId) {
                    $existingMark = StudentSubjectMark::find($markId);
                    if ($existingMark && $existingMark->proof) {
                        $this->fileUploadService->delete($existingMark->proof, $existingMark->proof_disk);
                    }
                }

                $markData['proof'] = $this->fileUploadService->upload(
                    $markData['proof'],
                    'students/marks-proofs',
                    $disk
                );
                $markData['proof_disk'] = $disk;
            } else {
                // Keep existing proof if no new file uploaded and it's an update
                if ($markId) {
                    unset($markData['proof']);
                    unset($markData['proof_disk']);
                } else {
                    $markData['proof'] = null;
                    $markData['proof_disk'] = null;
                }
            }

            if ($markId) {
                $newMarkIds[] = $markId;
                StudentSubjectMark::where('id', $markId)->update($markData);
            } else {
                $markData['student_id'] = $student->id;
                $newMark = StudentSubjectMark::create($markData);
                $newMarkIds[] = $newMark->id;
            }
        }

        // Delete marks that were removed
        $marksToDelete = array_diff($existingMarkIds, $newMarkIds);
        if (! empty($marksToDelete)) {
            $marksToCleanup = StudentSubjectMark::whereIn('id', $marksToDelete)->get();
            foreach ($marksToCleanup as $mark) {
                if ($mark->proof) {
                    $this->fileUploadService->delete($mark->proof, $mark->proof_disk);
                }
                $mark->delete();
            }
        }
    }

    /**
     * Extract address data from student data array.
     */
    private function extractAddressData(array $data): array
    {
        return [
            'full_address' => $data['full_address'],
            'street_number' => $data['street_number'] ?? null,
            'street_name' => $data['street_name'] ?? null,
            'city' => $data['city'],
            'postcode' => $data['postcode'],
            'state' => $data['state'],
            'country' => $data['country'],
        ];
    }

    /**
     * Extract student data, removing nested structures.
     */
    private function extractStudentData(array $data): array
    {
        $studentData = $data;
        unset(
            $studentData['full_address'],
            $studentData['street_number'],
            $studentData['street_name'],
            $studentData['city'],
            $studentData['postcode'],
            $studentData['state'],
            $studentData['country'],
            $studentData['marks']
        );

        // Remove profile_picture if it's null or empty
        if (empty($studentData['profile_picture'])) {
            unset($studentData['profile_picture']);
        }

        return $studentData;
    }

    /**
     * Prepare marks data for bulk insert with file uploads.
     */
    private function prepareMarksForInsert(array $marksData, int $studentId, string $disk): array
    {
        // Get timestamp once for all records
        $now = now();

        return array_map(function ($markData) use ($studentId, $now, $disk) {
            $markData['student_id'] = $studentId;

            // Handle proof file upload
            if (isset($markData['proof']) && $markData['proof']) {
                $markData['proof'] = $this->fileUploadService->upload(
                    $markData['proof'],
                    'students/marks-proofs',
                    $disk
                );
                $markData['proof_disk'] = $disk;
            } else {
                $markData['proof'] = null;
                $markData['proof_disk'] = null;
            }

            // Add timestamps for bulk insert
            $markData['created_at'] = $now;
            $markData['updated_at'] = $now;

            return $markData;
        }, $marksData);
    }
}
