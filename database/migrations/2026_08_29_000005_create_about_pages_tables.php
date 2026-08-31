<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('folder_name')->nullable();
            $table->string('subtitle', 500)->nullable();
            $table->boolean('is_main_page_visible')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_main_page_visible', 'created_at']);
            $table->index(['type', 'folder_name']);
        });

        Schema::create('about_forum_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_id')->unique()->constrained('about_pages')->cascadeOnDelete();
            $table->longText('overview')->nullable();
            $table->string('forums_since_2015')->nullable();
            $table->string('participants')->nullable();
            $table->string('countries')->nullable();
            $table->string('organizations')->nullable();
            $table->json('backgrounds')->nullable();
            $table->json('objectives')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_forum_details');
        Schema::dropIfExists('about_pages');
    }
};
