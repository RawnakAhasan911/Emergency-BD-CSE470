<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrimeReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This matches all the columns we defined in the crime_reports migration.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'is_anonymous', // Feature 8: Anonymous Reporting
        'victim_name',
        'crime_type',
        'intensity',    // Feature 14: Keep track of crime type and intensity
        'description',
        'media_path',   // Feature 17: Media Support
        'media_link',
        'latitude',     // Feature 11: Dual Mode Location
        'longitude',
        'area',         // Feature 12: Reverse Geocoding Autofill
        'city',
        'status',       // Feature 18: Admin Moderation ('pending', 'approved', 'rejected')
    ];

    /**
     * The attributes that should be cast to native types.
     * This makes sure Laravel handles boolean checkboxes and math correctly.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'intensity' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Define the relationship with the User.
     * A crime report belongs to a single user (or null if guest).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}