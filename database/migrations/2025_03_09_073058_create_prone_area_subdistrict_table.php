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
        Schema::create('prone_area_subdistrict', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prone_area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subdistrict_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prone_area_subdistrict');
    }
};
