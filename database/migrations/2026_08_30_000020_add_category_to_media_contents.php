<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_contents', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('categories')
                ->nullOnDelete();
        });

        if (! DB::table('categories')->where('code', Category::GROUP_CODE_PHOTO_GALLERY)->exists()) {
            DB::table('categories')->insert([
                'parent_id' => null,
                'code' => Category::GROUP_CODE_PHOTO_GALLERY,
                'name' => 'Photo Gallery',
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
        Schema::table('media_contents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        $group = DB::table('categories')->where('code', Category::GROUP_CODE_PHOTO_GALLERY)->first();
        if ($group && ! DB::table('categories')->where('parent_id', $group->id)->exists()) {
            DB::table('categories')->where('id', $group->id)->delete();
        }
    }
};
