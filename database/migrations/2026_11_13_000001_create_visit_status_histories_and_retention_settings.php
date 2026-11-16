<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['visit_id', 'created_at']);
            $table->index('created_at');
        });

        Schema::create('retention_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('visit_retention_days')->default(365);
            $table->unsignedSmallInteger('photo_retention_days')->default(365);
            $table->timestamps();
        });

        DB::table('retention_settings')->insert([
            'id' => 1,
            'visit_retention_days' => 365,
            'photo_retention_days' => 365,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('retention_settings');
        Schema::dropIfExists('visit_status_histories');
    }
};
