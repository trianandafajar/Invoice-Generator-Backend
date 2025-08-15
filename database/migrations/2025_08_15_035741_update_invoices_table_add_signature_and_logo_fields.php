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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['signature_image_path', 'logo_image_path']);
            $table->longText('signature')->nullable()->comment('Base64 signature data');
            $table->string('logo_path')->nullable()->comment('Path to uploaded logo file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['signature', 'logo_path']);
            $table->string('signature_image_path');
            $table->string('logo_image_path')->nullable();
        });
    }
};
