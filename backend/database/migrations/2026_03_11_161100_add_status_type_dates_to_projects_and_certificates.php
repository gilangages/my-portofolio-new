<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add start_date, end_date, status, type to projects
        Schema::table('projects', function (Blueprint $table) {
            $table->date('start_date')->default('2025-01-01')->after('is_featured');
            $table->date('end_date')->default('2025-01-01')->after('start_date');
            $table->string('status', 50)->default('completed')->after('end_date');
            $table->string('type', 50)->nullable()->after('status');
        });

        // Add start_date, end_date, type to certificates
        Schema::table('certificates', function (Blueprint $table) {
            $table->date('start_date')->default('2025-01-01')->after('is_featured');
            $table->date('end_date')->default('2025-01-01')->after('start_date');
            $table->string('type', 50)->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'status', 'type']);
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'type']);
        });
    }
};
