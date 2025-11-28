<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForecastTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'scheduled_at',
        'minutes',
        'icalUID',
        'is_service',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getScheduledAtWeekAttribute(): string
    {
        return Carbon::parse($this->scheduled_at)->format('W');
    }
}
