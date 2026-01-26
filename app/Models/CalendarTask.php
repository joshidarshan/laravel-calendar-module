<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CalendarTask extends Model
{
    protected $fillable = [
        'title',
        'description',
        'task_datetime',
        'task_type',
        'repeat_type',
        'start_date',
        'repeat_end_date',
        'completed_occurrences'
    ];

    protected $casts = [
        'task_datetime' => 'datetime',
        'start_date' => 'date',
        'repeat_end_date' => 'date',
        'completed_occurrences' => 'array',
    ];

    // Static colors per type
    public function getColorAttribute()
    {
        return match($this->task_type) {
            'task' => '#3b82f6',
            'event' => '#f97316',
            'meeting' => '#382b57',
            'other' => '#6b7280',
            default => '#4f46e5',
        };
    }

    public function getTextColorAttribute()
    {
        return '#ffffff';
    }

    /**
     * Return the completed occurrence for a Y-m-d date (or null).
     */
    public function getCompletedOccurrence(string|Carbon $date): ?array
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');
        $occ = $this->completed_occurrences ?? [];
        foreach ($occ as $o) {
            if (isset($o['date']) && $o['date'] === $dateStr) return $o;
        }
        return null;
    }

    public function isCompletedOn(string|Carbon $date): bool
    {
        return (bool)$this->getCompletedOccurrence($date);
    }

    /**
     * Mark a particular date as completed. Accepts optional $completedAt and $note.
     * Stores entries as { date: "YYYY-MM-DD", completed_at: "YYYY-MM-DD HH:MM:SS", note: "..." }
     */
    public function markCompleted(string|Carbon $date, ?string $completedAt = null, ?string $note = null): bool
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');
        $completedAt = $completedAt ? Carbon::parse($completedAt)->toDateTimeString() : Carbon::now()->toDateTimeString();

        $occ = $this->completed_occurrences ?? [];
        foreach ($occ as &$o) {
            if (isset($o['date']) && $o['date'] === $dateStr) {
                // update completed_at and note
                $o['completed_at'] = $completedAt;
                if (!is_null($note)) $o['note'] = $note;
                $this->completed_occurrences = array_values($occ);
                return $this->save();
            }
        }

        $entry = ['date' => $dateStr, 'completed_at' => $completedAt];
        if (!is_null($note)) $entry['note'] = $note;
        $occ[] = $entry;
        $this->completed_occurrences = array_values($occ);
        return $this->save();
    }

    /**
     * Unmark completion for a particular date.
     */
    public function unmarkCompleted(string|Carbon $date): bool
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');
        $occ = $this->completed_occurrences ?? [];
        $new = [];
        $removed = false;
        foreach ($occ as $o) {
            if (isset($o['date']) && $o['date'] === $dateStr) {
                $removed = true;
                continue;
            }
            $new[] = $o;
        }
        if ($removed) {
            $this->completed_occurrences = count($new) ? array_values($new) : null;
            return $this->save();
        }
        return false;
    }
}