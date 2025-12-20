<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'marks.*.subject_id' => ['required', 'integer', 'exists:subjects,id', 'distinct'],
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
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name must not exceed 50 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name must not exceed 50 characters.',
            'birth_date.required' => 'Birth date is required.',
            'birth_date.date' => 'Birth date must be a valid date.',
            'birth_date.before' => 'Birth date must be before today.',
            'standard.required' => 'Standard is required.',
            'standard.integer' => 'Standard must be an integer.',
            'standard.min' => 'Standard must be at least 1.',
            'standard.max' => 'Standard must not exceed 12.',
            'status.required' => 'Status is required.',
            'status.integer' => 'Status must be an integer.',
            'status.in' => 'Status must be either Active (1) or Inactive (0).',
            'profile_picture.image' => 'Profile picture must be an image.',
            'profile_picture.mimes' => 'Profile picture must be an image file (jpg, jpeg, png, gif, bmp, webp, svg).',
            'profile_picture.max' => 'Profile picture must not exceed 2MB.',

            'full_address.required' => 'Full address is required.',
            'full_address.min' => 'Full address must be at least 3 characters.',
            'full_address.max' => 'Full address must not exceed 1000 characters.',
            'street_number.max' => 'Street number must not exceed 50 characters.',
            'street_name.max' => 'Street name must not exceed 50 characters.',
            'city.required' => 'City is required.',
            'city.min' => 'City must be at least 2 characters.',
            'city.max' => 'City must not exceed 50 characters.',
            'postcode.required' => 'Postcode is required.',
            'postcode.min' => 'Postcode must be at least 4 characters.',
            'postcode.max' => 'Postcode must not exceed 30 characters.',
            'state.required' => 'State is required.',
            'state.min' => 'State must be at least 2 characters.',
            'state.max' => 'State must not exceed 50 characters.',
            'country.required' => 'Country is required.',
            'country.min' => 'Country must be at least 2 characters.',
            'country.max' => 'Country must not exceed 50 characters.',

            'marks.required' => 'At least one subject mark entry is required.',
            'marks.array' => 'Marks must be an array.',
            'marks.min' => 'At least one subject mark entry is required.',
            'marks.*.subject_id.required' => 'Subject is required.',
            'marks.*.subject_id.integer' => 'Subject must be an integer.',
            'marks.*.subject_id.exists' => 'Selected subject does not exist.',
            'marks.*.subject_id.distinct' => 'Each subject can only be added once per student.',
            'marks.*.total_marks.required' => 'Total marks is required.',
            'marks.*.total_marks.integer' => 'Total marks must be an integer.',
            'marks.*.total_marks.min' => 'Total marks must be at least 1.',
            'marks.*.total_marks.max' => 'Total marks must not exceed 1000.',
            'marks.*.obtained_marks.required' => 'Obtained marks is required.',
            'marks.*.obtained_marks.integer' => 'Obtained marks must be an integer.',
            'marks.*.obtained_marks.min' => 'Obtained marks cannot be negative.',
            'marks.*.proof.file' => 'Proof must be a file.',
            'marks.*.proof.mimes' => 'Proof must be an image or document file (PDF, Word, Excel, PowerPoint, Text, etc.).',
            'marks.*.proof.max' => 'Proof file must not exceed 10MB.',
        ];
    }
}
