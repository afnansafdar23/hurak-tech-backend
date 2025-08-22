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
        Schema::create('boxes', function (Blueprint $table) {
             $table->id();
                      $table->unsignedInteger('height');           // e.g. pixels
                      $table->unsignedInteger('width');            // e.g. pixels
                      $table->string('color', 7);                  // hex like #A1B2C3
                      $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
