<?php
// database/migrations/2026_04_30_000001_create_payment_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('application_documents')->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount_paid', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('order_number');
            $table->index('payment_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_orders');
    }
};