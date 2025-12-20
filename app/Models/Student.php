<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_picture',
        'profile_picture_disk',
        'first_name',
        'last_name',
        'birth_date',
        'standard',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'status' => StudentStatus::class,
            'standard' => 'integer',
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function studentSubjectMarks(): HasMany
    {
        return $this->hasMany(StudentSubjectMark::class);
    }
}
