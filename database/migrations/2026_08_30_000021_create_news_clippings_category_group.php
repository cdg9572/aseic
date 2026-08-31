<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('categories')->where('code', Category::GROUP_CODE_NEWS_CLIPPINGS)->exists()) {
            DB::table('categories')->insert([
                'parent_id' => null,
                'code' => Category::GROUP_CODE_NEWS_CLIPPINGS,
                'name' => 'News Clippings',
                'depth' => 0,
                'display_order' => ((int) DB::table('categories')->whereNull('parent_id')->max('display_order')) + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $group = DB::table('categories')->where('code', Category::GROUP_CODE_NEWS_CLIPPINGS)->first();
        if ($group && ! DB::table('categories')->where('parent_id', $group->id)->exists()) {
            DB::table('categories')->where('id', $group->id)->delete();
        }
    }
};
