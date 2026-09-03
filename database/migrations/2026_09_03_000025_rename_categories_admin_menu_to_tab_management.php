<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_menus')) {
            return;
        }

        DB::table('admin_menus')
            ->where('id', 42)
            ->where('permission_key', 'settings.categories')
            ->update([
                'name' => '탭 관리',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_menus')) {
            return;
        }

        DB::table('admin_menus')
            ->where('id', 42)
            ->where('permission_key', 'settings.categories')
            ->where('name', '탭 관리')
            ->update([
                'name' => '코드 관리',
                'updated_at' => now(),
            ]);
    }
};
