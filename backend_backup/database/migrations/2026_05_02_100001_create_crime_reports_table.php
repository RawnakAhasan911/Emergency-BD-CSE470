<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crime_reports', function (Blueprint $table) {
            $table->id();
            
            // Link to the user who reported it. Nullable so guests can report.
            // Feature 20 (Danger Zone): 'set null' means if an admin deletes a user, their crimes stay on the map!
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); 
            
            // Feature 12: User can keep themselves anonymous
            $table->boolean('is_anonymous')->default(false); 
            
            // Core Reporting Fields
            $table->string('victim_name')->nullable(); 
            $table->string('crime_type'); 
            
            // Feature 14: Keep track of crime type and intensity
            $table->integer('intensity')->comment('Scale of 1 to 5'); 
            $table->text('description')->nullable(); 
            
            // Feature 17: Photo/video/news article as evidence
            $table->string('media_path')->nullable(); 
            $table->string('media_link')->nullable(); 
            
            // Feature 7 & 15: Location Access & Live Map Markers
            // Using decimal for high-precision GPS coordinates
            $table->decimal('latitude', 10, 8); 
            $table->decimal('longitude', 11, 8); 
            
            // Reverse Geocoding Autofill data
            $table->string('area')->nullable(); 
            $table->string('city')->nullable(); 
            
            // Feature 8: Admin Moderation Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crime_reports');
    }
};