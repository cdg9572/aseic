<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('main_pages', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_visible')->default(false);
            $table->string('folder_name', 120)->unique();
            $table->string('event_name');
            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->boolean('use_custom_event_date')->default(false);
            $table->string('event_date_text')->nullable();
            $table->foreignId('banner_id')->nullable()->constrained('banners')->nullOnDelete();
            $table->foreignId('popup_id')->nullable()->constrained('popups')->nullOnDelete();
            $table->string('programme_background_path', 500)->nullable();
            $table->string('programme_background_name')->nullable();
            $table->json('programme_items')->nullable();
            $table->string('register_background_path', 500)->nullable();
            $table->string('register_background_name')->nullable();
            $table->string('past_forum_video_url', 2048)->nullable();
            $table->json('host_images')->nullable();
            $table->json('organizer_images')->nullable();
            $table->json('co_organizer_images')->nullable();
            $table->string('footer_text', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_visible', 'created_at']);
            $table->index(['event_start_date', 'event_end_date']);
        });

        Schema::create('main_page_speaker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_page_id')->constrained('main_pages')->cascadeOnDelete();
            $table->foreignId('speaker_id')->constrained('speakers')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['main_page_id', 'speaker_id']);
            $table->index(['main_page_id', 'sort_order']);
        });

        Schema::create('main_page_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_page_id')->constrained('main_pages')->cascadeOnDelete();
            $table->string('slot', 80);
            $table->string('linkable_type');
            $table->unsignedBigInteger('linkable_id');
            $table->timestamps();

            $table->unique(['main_page_id', 'slot']);
            $table->index(['linkable_type', 'linkable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('main_page_links');
        Schema::dropIfExists('main_page_speaker');
        Schema::dropIfExists('main_pages');
    }
};
