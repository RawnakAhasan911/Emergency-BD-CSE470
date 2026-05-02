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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Feature 1: Role-Based Access Control
            // We include 'guest' here so guest reports can still technically be tracked if needed, 
            // though usually guests just have user_id = null in the reports table.
            $table->enum('role', ['admin', 'citizen', 'guest'])->default('citizen'); 
            
            // Feature 4: Account Status Control
            $table->boolean('is_suspended')->default(false); 
            
            // Feature 5 & 16: Default Points System
            $table->integer('points')->default(2); 

            $table->rememberToken();
            $table->timestamps();
        });

        // Standard Laravel table for password resets
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Standard Laravel table for tracking active sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};