<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('boards')
            ->where('slug', 'notices')
            ->update([
                'name' => 'Announcements',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('boards')
            ->where('slug', 'notices')
            ->where('name', 'Announcements')
            ->update([
                'name' => '공지사항',
                'updated_at' => now(),
            ]);
    }
};
