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
            $table->string('session_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->timestamps();
            
            $table->unique(['session_id', 'product_id']);
            $table->unique(['user_id', 'product_id']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
