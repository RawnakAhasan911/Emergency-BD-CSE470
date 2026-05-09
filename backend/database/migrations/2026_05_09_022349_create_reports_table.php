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
      Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who reported it
        
        // Feature 14: Type and Intensity
        $table->string('type'); 
        $table->string('intensity'); 
        
        // Feature 7 & 15: Coordinates for the map
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);
        
        // Feature 17: Evidence (Nullable because they might not upload a file)
        $table->string('evidence_path')->nullable();
        
        // Feature 12: Anonymity
        $table->boolean('is_anonymous')->default(false);
        
        // Feature 8: Admin Approval Pipeline
        $table->string('status')->default('pending'); // pending, approved, rejected
        
        $table->timestamps();
      });
    }
};
