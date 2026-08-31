<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_pages', function (Blueprint $table) {
            $table->string('page_title')->nullable()->after('type');
        });

        Schema::table('programme_pages', function (Blueprint $table) {
            $table->string('page_title')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('programme_pages', function (Blueprint $table) {
            $table->dropColumn('page_title');
        });

        Schema::table('about_pages', function (Blueprint $table) {
            $table->dropColumn('page_title');
        });
    }
};
