<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'project_id',
        'completed_at',
        'minutes',
        'icalUID',
        'is_service',
        'invoiced',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'invoiced' => 'datetime',
    ];

    protected static $logAttributes = ['name', 'description', 'project_id', 'completed_at', 'minutes', 'icalUID', 'is_service', 'invoiced'];

    protected static $logName = 'task';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'project_id', 'completed_at', 'minutes', 'icalUID'])
            ->useLogName('task');
    }

    // create relationships so that a task can be associated with a project

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getCompletedAtWeekAttribute()
    {
        return Carbon::parse($this->completed_at)->format('W');
    }
}
