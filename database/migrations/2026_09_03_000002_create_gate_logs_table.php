<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            // 'in' = tamu masuk melalui gate, 'out' = tamu checkout
            $table->enum('direction', ['in', 'out']);
            // Token yang discan (barcode masuk / barcode checkout)
            $table->string('token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_logs');
    }
};
