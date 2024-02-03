<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nisn');
            $table->string('nis');
            $table->string('nik');
            $table->enum('gender', ['male', 'female']);
            $table->enum('religion', ['muslim', 'catholicism', 'christianity', 'hindu', 'buddhism', 'confucianism', 'other']);
            $table->string('full_name');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->enum('status_in_family', ['biological', 'adopted', 'stepchild']);
            $table->integer('child_order');
            $table->string('full_address');
            $table->string('origin_school');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_phone_number')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone_number')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone_number')->nullable();
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
