<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get assignments for this user
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_to_user_id');
    }

    /**
     * Get pending assignments
     */
    public function assignmentsInProgress(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_to_user_id')
            ->where('status', 'in_progress');
    }

    /**
     * Get completed assignments
     */
    public function assignmentsCompleted(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_to_user_id')
            ->where('status', 'completed');
    }
}

