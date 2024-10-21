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
        'appointment_type',
        'status',
        'adviser_signature',
        'chair_signature',
        'dean_signature',
        'disapproval_count',
        'completed_at',  // Make sure to include 'completed_at' in the fillable array if you want to mass assign it.
    ];

    // Casts for specific fields
    protected $casts = [
        'completed_at' => 'datetime', // Cast the 'completed_at' field to a datetime
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
}
