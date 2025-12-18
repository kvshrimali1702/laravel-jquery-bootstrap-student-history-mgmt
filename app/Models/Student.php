<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_picture',
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

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}
