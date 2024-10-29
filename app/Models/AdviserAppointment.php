<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdviserAppointment extends Model
{
    use HasFactory;

    protected $table = 'adviser_appointments';

    // Columns that are mass assignable
    protected $fillable = [
        'student_id',
        'adviser_id',
        'program_chair_id',  // Add this
        'appointment_type',
        'status',
        'adviser_signature',
        'chair_signature',
        'dean_signature',
        'disapproval_count',
        'completed_at',
        'consultation_dates', // Add this if you're mass assigning it
        'similarity_certificate',
        'similarity_manusccript',

    ];

    // Cast completed_at as a datetime
    protected $casts = [
        'completed_at' => 'datetime',
        'consultation_dates' => 'array',

    ];

    // Relationship: The appointment belongs to a student
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship: The appointment belongs to an adviser
    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    // Relationship: The appointment belongs to a program chair
    public function programChair()
    {
        return $this->belongsTo(User::class, 'program_chair_id');
    }
    public function getConsultationDatesAttribute($value)
    {
        return $value ? json_decode($value) : [];
    }
}

