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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // For events
            $table->string('topic')->nullable(); // For workshops
            $table->enum('activity_type', ['EVENT', 'WORKSHOP'])->default('EVENT');
            $table->date('start_date');
            $table->time('start_time');
            $table->string('created_by')->nullable(); // For events
            $table->string('instructor')->nullable(); // For workshops
            $table->string('venue');
            $table->string('status')->default('Open'); // Open, Confirmed, Cancelled
            $table->integer('max_pax')->nullable();
            $table->boolean('is_published')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
