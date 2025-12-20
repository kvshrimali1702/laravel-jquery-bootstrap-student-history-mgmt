<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSubjectMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'total_marks',
        'obtained_marks',
        'proof',
        'proof_disk',
    ];

    protected function casts(): array
    {
        return [
            'total_marks' => 'integer',
            'obtained_marks' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
