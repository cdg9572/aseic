<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_partners', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('position')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('profile_image_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_image_visible')->default(false);
            $table->longText('content')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active', 'created_at']);
            $table->index(['type', 'last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_partners');
    }
};
