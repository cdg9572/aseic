<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_venue_details', function (Blueprint $table) {
            $table->string('postal_code', 20)->nullable()->after('forum_location');
            $table->string('address_detail', 500)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('about_venue_details', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'address_detail']);
        });
    }
};
