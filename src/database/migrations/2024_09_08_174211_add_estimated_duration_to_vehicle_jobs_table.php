<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicle_jobs', function (Blueprint $table) {
            $table->integer('estimated_duration')->nullable()->after('status'); // Adding column after 'status'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_jobs', function (Blueprint $table) {
            $table->dropColumn('estimated_duration'); // Dropping column on rollback
        });
    }
};
