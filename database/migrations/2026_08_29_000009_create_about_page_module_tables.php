<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_steering_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_id')->constrained('about_pages')->cascadeOnDelete();
            $table->foreignId('homepage_partner_id')->constrained('homepage_partners')->cascadeOnDelete();
            $table->string('group_type', 20);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['about_page_id', 'homepage_partner_id', 'group_type'], 'about_steering_partner_unique');
            $table->index(['about_page_id', 'group_type', 'sort_order'], 'about_steering_partner_order');
        });

        Schema::create('about_co_organizer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_id')->constrained('about_pages')->cascadeOnDelete();
            $table->string('logo_path', 500)->nullable();
            $table->string('logo_name')->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('url', 2048)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['about_page_id', 'sort_order']);
        });

        Schema::create('about_venue_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_id')->unique()->constrained('about_pages')->cascadeOnDelete();
            $table->string('forum_location')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('venue_name')->nullable();
            $table->longText('venue_description')->nullable();
            $table->string('event_date')->nullable();
            $table->string('format', 30)->nullable();
            $table->longText('bus_content')->nullable();
            $table->longText('subway_content')->nullable();
            $table->longText('taxi_content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_venue_details');
        Schema::dropIfExists('about_co_organizer_items');
        Schema::dropIfExists('about_steering_partners');
    }
};
