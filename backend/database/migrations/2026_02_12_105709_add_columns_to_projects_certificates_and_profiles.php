<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah is_featured ke projects
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('description');
        });

        // Tambah is_featured ke certificates
        Schema::table('certificates', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('description');
        });

        // Tambah secondary_image ke profiles
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('secondary_image')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('secondary_image');
        });
    }
};
