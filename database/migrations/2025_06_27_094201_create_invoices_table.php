<?php

use Brick\Math\BigInteger;
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
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('client_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->decimal('remaining_price', 10, 2)->default(0);
            $table->string('status')->default(0); // e.g., pending, paid
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade');
            $table->string('sale_type'); // e.g., one-time, subscription
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
