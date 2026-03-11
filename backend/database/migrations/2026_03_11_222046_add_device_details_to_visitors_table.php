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
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('device_type')->nullable()->after('device_id');
            $table->string('os')->nullable()->after('device_type');
            $table->string('browser')->nullable()->after('os');
            $table->string('device_name')->nullable()->after('browser');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'os', 'browser', 'device_name']);
        });
    }
};
