<?php

namespace App\Http\Controllers;

use App\Services\SubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct(
        private readonly SubjectService $subjectService
    ) {}

    /**
     * Get all subjects or search subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $subjects = $search
            ? $this->subjectService->searchByName($search)
            : $this->subjectService->getAll();

        return response()->json([
            'results' => $subjects->map(fn ($subject) => [
                'id' => $subject->id,
                'text' => $subject->name,
            ]),
        ]);
    }
}
