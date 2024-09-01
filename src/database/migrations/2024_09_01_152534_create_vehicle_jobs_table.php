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
        Schema::create('vehicle_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->foreignId('car_id')->nullable()->index();
            $table->foreignId('service_id')->nullable()->index();
            $table->boolean('is_deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_jobs');
    }
};
