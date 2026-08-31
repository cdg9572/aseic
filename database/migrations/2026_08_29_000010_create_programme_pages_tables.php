<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programme_pages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->longText('subtitle')->nullable();
            $table->longText('title')->nullable();
            $table->string('location')->nullable();
            $table->string('event_date')->nullable();
            $table->longText('content')->nullable();
            $table->string('book_title')->nullable();
            $table->string('book_file_path', 500)->nullable();
            $table->string('book_file_name')->nullable();
            $table->unsignedBigInteger('book_file_size')->nullable();
            $table->string('book_link', 2048)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'created_at']);
        });

        Schema::create('programme_page_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_page_id')->constrained('programme_pages')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number');
            $table->string('session_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['programme_page_id', 'day_number']);
        });

        Schema::create('programme_session_speaker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_page_session_id')->constrained('programme_page_sessions')->cascadeOnDelete();
            $table->foreignId('speaker_id')->constrained('speakers')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['programme_page_session_id', 'speaker_id'], 'programme_session_speaker_unique');
            $table->index(['programme_page_session_id', 'sort_order'], 'programme_session_speaker_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programme_session_speaker');
        Schema::dropIfExists('programme_page_sessions');
        Schema::dropIfExists('programme_pages');
    }
};
