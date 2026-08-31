<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->foreignId('parent_id')->nullable()->constrained('media_contents')->cascadeOnDelete();
            $table->string('page_title');
            $table->longText('subtitle')->nullable();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->date('published_date')->nullable();
            $table->string('link', 2048)->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('image_name')->nullable();
            $table->unsignedBigInteger('image_size')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'parent_id', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_contents');
    }
};
