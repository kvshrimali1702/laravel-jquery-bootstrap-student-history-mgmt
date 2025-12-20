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
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile_picture_disk')->nullable()->after('profile_picture');
        });

        Schema::table('student_subject_marks', function (Blueprint $table) {
            $table->string('proof_disk')->nullable()->after('proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('profile_picture_disk');
        });

        Schema::table('student_subject_marks', function (Blueprint $table) {
            $table->dropColumn('proof_disk');
        });
    }
};
