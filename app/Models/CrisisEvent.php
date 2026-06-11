<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrisisEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CrisisEventFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'crisis';

    protected $fillable = [
        'anonymous_id',
        'source',
        'severity',
    ];
}
