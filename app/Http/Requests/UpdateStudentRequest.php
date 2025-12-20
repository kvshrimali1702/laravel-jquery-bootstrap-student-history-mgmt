<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'birth_date' => ['required', 'date', 'before:today'],
            'standard' => ['required', 'integer', 'min:1', 'max:12'],
            'status' => ['required', 'integer', 'in:0,1'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,bmp,webp,svg', 'max:2048'],

            // Address fields
            'full_address' => ['required', 'string', 'min:3', 'max:1000'],
            'street_number' => ['nullable', 'string', 'max:50'],
            'street_name' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'min:2', 'max:50'],
            'postcode' => ['required', 'string', 'min:4', 'max:30'],
            'state' => ['required', 'string', 'min:2', 'max:50'],
            'country' => ['required', 'string', 'min:2', 'max:50'],

            // Student marks
            'marks' => ['required', 'array', 'min:1'],
            'marks.*.id' => ['nullable', 'integer', 'exists:student_subject_marks,id'],
            'marks.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'marks.*.total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'marks.*.obtained_marks' => ['required', 'integer', 'min:0'],
            'marks.*.proof' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt,ppt,pptx,xls,xlsx,odt,ods,odp,jpeg,jpg,png,gif,bmp,webp,svg', 'max:10240'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $marks = $this->input('marks', []);

            // Check for duplicate subjects and attach errors to each offending row
            $seenSubjectIds = [];
            foreach ($marks as $index => $mark) {
                $subjectId = $mark['subject_id'] ?? null;

                if ($subjectId === null || $subjectId === '') {
                    continue;
                }

                if (isset($seenSubjectIds[$subjectId])) {
                    $validator->errors()->add(
                        "marks.{$index}.subject_id",
                        'Each subject can only be added once per student.'
                    );
                } else {
                    $seenSubjectIds[$subjectId] = true;
                }
            }

            // Validate obtained_marks <= total_marks for each entry
            foreach ($marks as $index => $mark) {
                if (isset($mark['total_marks']) && isset($mark['obtained_marks'])) {
                    $totalMarks = (int) $mark['total_marks'];
                    $obtainedMarks = (int) $mark['obtained_marks'];

                    if ($obtainedMarks > $totalMarks) {
                        $validator->errors()->add(
                            "marks.{$index}.obtained_marks",
                            "Obtained marks ({$obtainedMarks}) cannot be greater than total marks ({$totalMarks})."
                        );
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return (new StoreStudentRequest)->messages();
    }
}
