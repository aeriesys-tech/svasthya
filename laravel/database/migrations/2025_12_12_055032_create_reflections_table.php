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
        Schema::create('reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('q1')->nullable(); // What happened today that stayed in your mind?
            $table->text('q2')->nullable(); // What went through your mind right after that?
            $table->string('q3')->nullable(); // How did that make you feel? (Good, Low, Upset, Neutral)
            $table->text('q4')->nullable(); // What did you do next?
            $table->text('q5')->nullable(); // If you look at it now, is there another way to see it?
            $table->integer('q6')->nullable(); // How do you feel now? (1-5 rating)
            $table->date('date')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reflections');
    }
};
