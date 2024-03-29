<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
