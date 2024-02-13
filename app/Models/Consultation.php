<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'consultation_category_id',
        'consultation_date',
        'description',
    ];

    public function consultationCategory()
    {
        return $this->belongsTo(ConsultationCategory::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
