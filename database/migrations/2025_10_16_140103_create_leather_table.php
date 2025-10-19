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
            $table->decimal('cow',5,3)->nullable();
            $table->integer('sheep')->nullable();
            $table->integer('goat')->nullable();
            $table->integer('loading_date'); // like 14040404
            // $table->date('loading_date_utc');
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
