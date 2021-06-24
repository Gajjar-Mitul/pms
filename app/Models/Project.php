<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';

    protected $fillable = [
        'title',
        'client_name',
        'budget',
        'deadline',
        'payment',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
