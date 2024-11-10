<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    // Add fields that are allowed for mass assignment
    protected $fillable = [
        'title',
        'content',
    ];
}
