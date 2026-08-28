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
        Schema::table('visits', function (Blueprint $table) {
            // extend status to include 'waiting'
            $table->enum('status', ['pending', 'waiting', 'active', 'completed', 'rejected'])
                ->default('pending')
                ->change();

            // type of visit: meeting/callback (menemui) or letter/package delivery
            $table->enum('visit_type', ['meet', 'deliver'])->default('meet')->after('status');

            // visitor's choice when delivering something.
            // hand_in = delivered directly to the employee, leave = left at front desk.
            $table->enum('delivery_pref', ['hand_in', 'leave'])->nullable()->after('visit_type');

            // Name of the receptionist who received a left/limited delivery.
            $table->string('received_by_name')->nullable()->after('delivery_pref');

            // Free-text note (e.g. reason for waiting).
            $table->text('status_note')->nullable()->after('received_by_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['visit_type', 'delivery_pref', 'received_by_name', 'status_note']);
            $table->enum('status', ['pending', 'active', 'completed', 'rejected'])
                ->default('pending')
                ->change();
        });
    }
};
