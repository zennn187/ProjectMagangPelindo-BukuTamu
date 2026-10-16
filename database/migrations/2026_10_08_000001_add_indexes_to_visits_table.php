<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index untuk mempercepat filter laporan & navigasi menu admin
     * (whereDate check_in_time, source, status, range created_at).
     */
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->index('source', 'visits_source_index');
            $table->index('status', 'visits_status_index');
            $table->index('check_in_time', 'visits_check_in_time_index');
            $table->index('created_at', 'visits_created_at_index');
            $table->index(['source', 'check_in_time'], 'visits_source_check_in_index');
            $table->index(['source', 'status'], 'visits_source_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex('visits_source_index');
            $table->dropIndex('visits_status_index');
            $table->dropIndex('visits_check_in_time_index');
            $table->dropIndex('visits_created_at_index');
            $table->dropIndex('visits_source_check_in_index');
            $table->dropIndex('visits_source_status_index');
        });
    }
};