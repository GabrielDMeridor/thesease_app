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
        'program_chair_id',
        'appointment_type',
        'status',
        'adviser_signature',
        'chair_signature',
        'dean_signature',
        'disapproval_count',
        'completed_at',
        'consultation_dates',
        'similarity_certificate',
        'similarity_manuscript',
        'community_extension_link',
        'community_extension_response',
        'community_extension_response_date',
        'signed_routing_form_1',
        'original_signed_routing_form_1',
        'proposal_manuscript',
        'original_proposal_manuscript',
        'proposal_video_presentation',
        'original_proposal_video_presentation',
        'submission_files_link',
        'submission_files_approval',
        'submission_files_response',
        'proposal_defense_date',
        'proposal_defense_time',
        'panel_members', // Add panel_members to fillable
        'schedule_type',
        'proposal_manuscript_updates',
        'panel_comments',
        'student_replies',
        'panel_remarks',
        'panel_signatures',
        'dean_monitoring_signature',
        'student_statistician_response',

    ];

    // Casts for specific fields
    protected $casts = [
        'completed_at' => 'datetime',
        'consultation_dates' => 'array',
        'panel_members' => 'array',
        'proposal_manuscript_updates' => 'array',
        'panel_comments' => 'array',
        'student_replies' => 'array',
        'panel_remarks' => 'array',
        'panel_signatures' => 'array'
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

    // Accessor for consultation dates
    public function getConsultationDatesAttribute($value)
    {
        return $value ? json_decode($value) : [];
    }
}
