<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_photos', function (Blueprint $table) {
            $table->foreignId('visit_id')->primary()->constrained()->cascadeOnDelete();
            $table->longText('contents')->charset('binary');
            $table->string('mime_type', 100)->default('image/jpeg');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_photos');
    }
};
