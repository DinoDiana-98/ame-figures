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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            
            // ✅ Para usuarios NO logueados (usando session)
            $table->string('session_id')->nullable();
            
            // ✅ Para usuarios logueados
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // ✅ Información del producto
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // ✅ Datos del carrito
            $table->integer('cantidad')->default(1);
            $table->timestamps();
            
            // ✅ Índices únicos para evitar duplicados
            $table->unique(['session_id', 'product_id']);
            $table->unique(['user_id', 'product_id']);
        });

        // ✅ Agregar índices simples POR SEPARADO para evitar el error
        Schema::table('carts', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('user_id');
        });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
