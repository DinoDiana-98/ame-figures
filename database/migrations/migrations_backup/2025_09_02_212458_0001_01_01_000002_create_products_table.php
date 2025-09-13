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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('imagen1')->nullable();
            $table->string('imagen2')->nullable();
            $table->string('imagen3')->nullable();
            $table->string('material')->nullable();
            $table->string('size')->nullable();
            $table->text('incluye')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('precio_descuento', 10, 2)->nullable();
            $table->integer('stock_minimo')->default(5);
            $table->boolean('destacado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
