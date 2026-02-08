<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Profile (About Me + CV) - Hanya 1 baris data (Singleton)
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('job_title');
            $table->text('about_description');
            $table->string('photo_path')->nullable();
            $table->string('cv_path')->nullable(); // Link PDF
            $table->timestamps();
        });

        // 2. Tabel Skills (Master Data: Nama + Icon)
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Laravel
            $table->string('identifier'); // e.g., icons/laravel.svg
            $table->string('category')->default('tech'); // frontend/backend/tools
            $table->timestamps();
        });

        // 3. Tabel Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail_path');
            $table->string('live_demo_link')->nullable();
            $table->string('repository_link')->nullable(); // Github/Gitfront
            $table->timestamps();
        });

        // 4. Tabel Pivot (Hubungan Project <-> Skills)
        // Ini yang bikin project otomatis punya icon & nama skill tanpa input ulang
        Schema::create('project_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
        });

        // 5. Tabel Experiences
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('role');
            $table->string('status'); // Work, Intern, Freelance
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null = Present
            $table->text('description');
            $table->timestamps();
        });

        // 6. Tabel Certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('issuer')->nullable(); // Penerbit
            $table->text('description');
            $table->string('image_path');
            $table->string('credential_link')->nullable();
            $table->timestamps();
        });

        // 7. Tabel Contacts
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name'); // Instagram, LinkedIn
            $table->string('url');
            $table->string('icon_path')->nullable(); // Bisa upload icon custom
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('project_skill');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('profiles');
    }
};
