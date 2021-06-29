<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;
    protected $table = 'project_milestones';

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'amount',
        'deadline',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
