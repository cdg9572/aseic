<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('address_book_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_book_id')->constrained('address_books')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->boolean('is_subscribed')->default(true);
            $table->timestamps();

            $table->unique(['address_book_id', 'email']);
        });

        Schema::create('mail_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('reply_name')->nullable();
            $table->string('reply_email')->nullable();
            $table->string('subject');
            $table->string('target_type', 30);
            $table->longText('direct_recipients')->nullable();
            $table->string('subscription_status', 30)->default('subscribed');
            $table->longText('content');
            $table->json('attachments')->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
        });

        Schema::create('mail_campaign_address_book', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_campaign_id')->constrained('mail_campaigns')->cascadeOnDelete();
            $table->foreignId('address_book_id')->constrained('address_books')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mail_campaign_id', 'address_book_id'], 'mail_campaign_address_book_unique');
        });

        Schema::create('mail_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_campaign_id')->constrained('mail_campaigns')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('status', 30)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['mail_campaign_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_campaign_recipients');
        Schema::dropIfExists('mail_campaign_address_book');
        Schema::dropIfExists('mail_campaigns');
        Schema::dropIfExists('address_book_contacts');
        Schema::dropIfExists('address_books');
    }
};
