<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class User extends Authenticatable
{
    const SuperAdmin = 1;
    const Admin = 2;
    const GraduateSchool = 3;
    const ProgramChair = 4;
    const Thesis_DissertationProfessor = 5;
    const AufEthicsReviewCommittee = 6;
    const Statistician = 7;
    const OVPRI = 8;
    const Library = 9;
    const LanguageEditor = 10;
    const GraduateSchoolStudent = 11;

    // Add Thesis/Dissertation-related constants

    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'account_type', 'degree', 'program', 'nationality',
        'immigration_or_studentvisa', 'routing_form_one', 'original_routing_form_one_filename',
        'manuscript', 'original_manuscript_filename', 'adviser_appointment_form', 'original_adviser_appointment_form_filename'
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function getAccountTypeName($type)
    {
        $types = [
            self::SuperAdmin => 'SuperAdmin',
            self::Admin => 'Admin',
            self::Thesis_DissertationProfessor => 'Thesis/Dissertation Professor',
            self::ProgramChair => 'Program Chair',
            self::Library => 'Library',
            self::AufEthicsReviewCommittee => 'AUF Ethics Review Committee',
            self::Statistician => 'Statistician',
            self::OVPRI => 'OVPRI',
            self::LanguageEditor => 'Language Editor',
            self::GraduateSchool => 'Graduate School',
            self::GraduateSchoolStudent => 'Graduate School Student',
        ];

        return $types[$type] ?? 'Unknown';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adviserAppointment()
    {
        return $this->hasOne(AdviserAppointment::class, 'student_id');
    }

    /**
     * If a user is an adviser, get all the students they are advising.
     */
    public function advisingStudents()
    {
        return $this->hasMany(AdviserAppointment::class, 'adviser_id');
    }


}
