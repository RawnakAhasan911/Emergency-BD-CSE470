<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // This line is the magic key. It tells Laravel: "Allow data to be saved in these exact columns."
    protected $fillable = [
        'user_id',
        'type',
        'intensity',
        'latitude',
        'longitude',
        'evidence_path',
        'is_anonymous',
        'status',
    ];

    // Optional but highly recommended: Link this report back to the User who made it
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}