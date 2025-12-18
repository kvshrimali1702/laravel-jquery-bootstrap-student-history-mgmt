<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'full_address',
        'street_number',
        'street_name',
        'city',
        'postcode',
        'state',
        'country',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
