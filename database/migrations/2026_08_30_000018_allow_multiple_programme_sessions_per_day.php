<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programme_page_sessions', function (Blueprint $table) {
            $table->index(
                ['programme_page_id', 'day_number', 'sort_order'],
                'programme_page_sessions_day_order_index',
            );
        });

        Schema::table('programme_page_sessions', function (Blueprint $table) {
            $table->dropUnique('programme_page_sessions_programme_page_id_day_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('programme_page_sessions', function (Blueprint $table) {
            $table->unique(['programme_page_id', 'day_number']);
        });

        Schema::table('programme_page_sessions', function (Blueprint $table) {
            $table->dropIndex('programme_page_sessions_day_order_index');
        });
    }
};
