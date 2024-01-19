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
        Schema::create('media_storages', function (Blueprint $table) {
            $table->id();
            $table->string('model_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->string('public_path')->nullable();
            $table->string('storage_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_storages');
    }
};
