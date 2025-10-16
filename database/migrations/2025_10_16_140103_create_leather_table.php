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
        Schema::create('leather', function (Blueprint $table) {
            $table->id();
            $table->foreignId('butcher_id')->constrained()->onDelete('cascade');
            $table->decimal('cow',5,3)->default(0);
            $table->integer('sheep')->default(0);
            $table->integer('goat')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leather');
    }
};
