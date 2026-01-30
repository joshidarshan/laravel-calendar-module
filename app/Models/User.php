<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens; // <-- Add this

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- Add HasApiTokens here

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_to_user_id');
    }

    public function assignmentsInProgress(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_to_user_id')
                    ->where('status', 'in_progress');
    }

    public function assignmentsCompleted(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_to_user_id')
                    ->where('status', 'completed');
    }
}
