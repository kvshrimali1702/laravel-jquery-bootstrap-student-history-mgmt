<?php

use App\Enums\StudentStatus;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->text('profile_picture')->nullable();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->date('birth_date');
            $table->unsignedTinyInteger('standard');
            $table->enum('status', array_map(fn ($case) => (string) $case->value, StudentStatus::cases()))->default((string) StudentStatus::Active->value); // StudentStatus enum: 1 for active, 0 for inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
