<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_title');
            $table->longText('subtitle')->nullable();
            $table->string('participation_mode', 30)->default('participating');
            $table->string('period_text')->nullable();
            $table->string('guide_step_1')->nullable();
            $table->string('guide_step_2')->nullable();
            $table->string('guide_step_3')->nullable();
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->boolean('use_custom_end_text')->default(false);
            $table->string('registration_end_text')->nullable();
            $table->longText('closed_notice')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('registration_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_page_id')->constrained('registration_pages')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('country')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('position')->nullable();
            $table->string('participation_type', 30)->nullable();
            $table->string('status', 30)->default('pending');
            $table->text('note')->nullable();
            $table->boolean('agreed_privacy')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['registration_page_id', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_applicants');
        Schema::dropIfExists('registration_pages');
    }
};
