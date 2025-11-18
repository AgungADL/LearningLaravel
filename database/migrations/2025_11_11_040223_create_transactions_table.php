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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null'); // Jika member dihapus, jangan hapus transaksi
            
            $table->integer('total_items');
            $table->integer('subtotal');
            $table->integer('discount_amount')->default(0);
            $table->integer('grand_total');
            
            $table->integer('paid_amount');
            $table->integer('change_amount');
            $table->string('payment_method')->default('Cash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
