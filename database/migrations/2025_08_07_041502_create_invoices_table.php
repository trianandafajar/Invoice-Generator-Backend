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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->date('process_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('customer_name');
            $table->string('customer_id')->nullable();
            $table->text('customer_address')->nullable();
            $table->decimal('previous_balance', 15, 2)->nullable();
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('payment_account');
            $table->string('contact_email');
            $table->text('notes')->nullable();
            $table->string('signature_image_path');
            $table->string('logo_image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
