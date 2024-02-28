<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'nis',
        'nik',
        'gender',
        'religion',
        'full_name',
        'birth_date',
        'birth_place',
        'status_in_family',
        'child_order',
        'full_address',
        'origin_school',
        'phone_number',
        'email',
        'father_name',
        'father_phone_number',
        'mother_name',
        'mother_phone_number',
        'guardian_name',
        'guardian_phone_number',
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function achievements()
    {
        return $this->hasMany(StudentAchievement::class);
    }
}
