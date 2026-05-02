<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Required for API authentication

class User extends Authenticatable
{
    // HasApiTokens is essential for the login/register token generation
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Includes all the custom fields we added in the migration.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // Feature 1: Role-Based Access Control
        'is_suspended',  // Feature 4: Account Status Control
        'points',        // Feature 5 & 16: Default Points System
    ];

    /**
     * The attributes that should be hidden for serialization.
     * This ensures passwords and tokens don't leak in API responses.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * Ensures Laravel treats the data types correctly when pulling from the DB.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'points' => 'integer',
        ];
    }

    /**
     * Define the relationship with Crime Reports.
     * A user can have multiple crime reports.
     */
    public function crimeReports()
    {
        return $this->hasMany(CrimeReport::class);
    }
}