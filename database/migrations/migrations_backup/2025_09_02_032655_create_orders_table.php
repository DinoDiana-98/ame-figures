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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pendiente');
            $table->text('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->string('nombre_producto');
            $table->decimal('precio_unitario', 10, 2);
            $table->integer('cantidad');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
