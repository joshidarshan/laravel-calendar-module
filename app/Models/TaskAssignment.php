<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAssignment extends Model
{
    protected $fillable = [
        'calendar_task_id',
        'assigned_to_user_id',
        'status',
        'notes',
        'priority',
        'assigned_at',
        'started_at',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress',
        'attachments',
        'target_date',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'attachments' => 'array',
    ];

    /**
     * Get the calendar task this assignment belongs to
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(CalendarTask::class, 'calendar_task_id');
    }

    /**
     * Get the user this task is assigned to
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Scope to get pending assignments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get in-progress assignments
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Start the assignment
     */
    public function start(): bool
    {
        $this->status = 'in_progress';
        $this->started_at = now();
        return $this->save();
    }

    /**
     * Complete the assignment
     */
    public function complete(int $actualHours = null): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->progress = 100;
        if ($actualHours !== null) {
            $this->actual_hours = $actualHours;
        }
        return $this->save();
    }

    /**
     * Hold the assignment
     */
    public function hold(): bool
    {
        $this->status = 'on_hold';
        return $this->save();
    }

    /**
     * Cancel the assignment
     */
    public function cancel(): bool
    {
        $this->status = 'cancelled';
        return $this->save();
    }

    /**
     * Update progress
     */
    public function updateProgress(float $percentage): bool
    {
        $this->progress = min(100, max(0, $percentage));
        return $this->save();
    }

    /**
     * Get remaining hours
     */
    public function getRemainingHours(): ?int
    {
        if (!$this->estimated_hours) return null;
        if (!$this->actual_hours) return $this->estimated_hours;
        return max(0, $this->estimated_hours - $this->actual_hours);
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            1 => 'High',
            2 => 'Urgent',
            default => 'Normal',
        };
    }

    /**
     * Get priority color
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            1 => '#ff9800',
            2 => '#f44336',
            default => '#4caf50',
        };
    }
}
