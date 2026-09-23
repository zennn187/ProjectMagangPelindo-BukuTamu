<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Sumber kedatangan: kiosk (form buku tamu biasa) atau gate (scan barcode security)
            $table->string('source', 10)->default('kiosk')->after('status');

            // Data tambahan tamu yang melewati security/gate
            $table->string('identity_number')->nullable()->after('visitor_institution');
            $table->string('plate_number')->nullable()->after('identity_number');
            $table->string('person_to_meet')->nullable()->after('plate_number');

            // Token barcode checkout sekali pakai (dikirim via WhatsApp)
            $table->string('checkout_token')->nullable()->unique()->after('qr_code_token');
            $table->dateTime('checkout_used_at')->nullable()->after('check_out_time');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'identity_number',
                'plate_number',
                'person_to_meet',
                'checkout_token',
                'checkout_used_at',
            ]);
        });
    }
};
