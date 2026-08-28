<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('join_type', 20)->nullable();
            $table->string('login_id')->unique()->nullable();  // 로그인 ID
            $table->enum('role', ['super_admin', 'admin', 'manager', 'user'])->default('user');  // 사용자 역할
            $table->foreignId('admin_group_id')->nullable()->comment('관리자 권한 그룹 ID');
            $table->boolean('is_active')->default(true);  // 활성화 여부
            $table->timestamp('last_login_at')->nullable();  // 마지막 로그인 시간
            $table->timestamp('withdrawn_at')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number', 30)->nullable();
            $table->string('birth_date', 8)->nullable();
            $table->string('address_postcode', 20)->nullable();
            $table->string('address_base')->nullable();
            $table->string('address_detail')->nullable();
            $table->string('school_name')->nullable();
            $table->boolean('is_school_representative')->default(false);
            $table->boolean('email_marketing_consent')->default(false);
            $table->timestamp('email_marketing_consent_at')->nullable();
            $table->boolean('kakao_marketing_consent')->default(false);
            $table->timestamp('kakao_marketing_consent_at')->nullable();
            $table->boolean('sms_marketing_consent')->default(false);
            $table->timestamp('terms_agreed_at')->nullable();
            $table->string('department')->nullable()->comment('부서');
            $table->string('position')->nullable()->comment('직위');
            $table->string('contact', 50)->nullable()->comment('연락처');
            $table->rememberToken();
            $table->timestamps();

            $table->index('join_type');
            $table->index('phone_number');
            $table->index('withdrawn_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
