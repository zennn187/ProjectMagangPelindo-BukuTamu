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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->string('visitor_institution');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('purpose');
            $table->string('photo_path')->nullable();
            $table->string('qr_code_token')->unique();
            $table->enum('status', ['pending', 'active', 'completed', 'rejected'])->default('pending');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
