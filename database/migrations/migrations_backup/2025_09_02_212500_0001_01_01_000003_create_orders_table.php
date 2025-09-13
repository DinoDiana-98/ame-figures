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
            $table->string('session_id'); // Para identificar pedidos sin usuario
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('customer_name'); // Cambiado de nombre_cliente
            $table->string('customer_phone'); // Nuevo campo
            $table->string('customer_email')->nullable(); // Cambiado de email_cliente y nullable
            $table->string('department'); // Nuevo campo
            $table->string('city'); // Nuevo campo
            $table->string('district')->nullable(); // Nuevo campo
            $table->text('reference')->nullable(); // Nuevo campo (punto de referencia)
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending'); // Cambiado de estado
            $table->text('notes')->nullable();
            $table->text('whatsapp_message'); // Para guardar el mensaje enviado
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->string('product_name'); // Cambiado de nombre_producto
            $table->decimal('unit_price', 10, 2); // Cambiado de precio_unitario
            $table->integer('quantity'); // Cambiado de cantidad
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
