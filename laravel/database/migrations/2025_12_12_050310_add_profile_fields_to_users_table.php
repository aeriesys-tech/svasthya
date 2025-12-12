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
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('email');
            $table->string('phone')->nullable()->after('dob');
            $table->enum('gender', ['Male', 'Female', 'Others'])->nullable()->after('phone');
            $table->string('designation')->nullable()->after('gender');
            $table->string('division')->nullable()->after('designation');
            $table->string('current_working')->nullable()->after('division');
            $table->string('qualification')->nullable()->after('current_working');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dob', 'phone', 'gender', 'designation', 'division', 'current_working', 'qualification']);
        });
    }
};
