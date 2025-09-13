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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique(); // ✅ Para URLs amigables
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->integer('orden')->default(0);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
