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
        Schema::table('programme_pages', function (Blueprint $table): void {
            $table->foreignId('category_id')
                ->nullable()
                ->after('type')
                ->constrained('categories')
                ->nullOnDelete();
        });

        foreach ($this->groups() as $code => $name) {
            if (DB::table('categories')->where('code', $code)->exists()) {
                continue;
            }

            DB::table('categories')->insert([
                'parent_id' => null,
                'code' => $code,
                'name' => $name,
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
        Schema::table('programme_pages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('category_id');
        });

        foreach (array_keys($this->groups()) as $code) {
            $group = DB::table('categories')->where('code', $code)->first();
            if ($group && ! DB::table('categories')->where('parent_id', $group->id)->exists()) {
                DB::table('categories')->where('id', $group->id)->delete();
            }
        }
    }

    /** @return array<string, string> */
    private function groups(): array
    {
        return [
            Category::GROUP_CODE_ARCHIVE_THEME => 'Theme',
            Category::GROUP_CODE_ARCHIVE_PROGRAMME => 'Programme',
            Category::GROUP_CODE_YOUTUBE_CHANNEL => 'YouTube Channel',
        ];
    }
};
