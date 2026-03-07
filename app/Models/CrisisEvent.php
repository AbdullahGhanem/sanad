<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrisisEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CrisisEventFactory> */
    use HasFactory;

    protected $fillable = [
        'anonymous_id',
        'source',
        'severity',
    ];
}
