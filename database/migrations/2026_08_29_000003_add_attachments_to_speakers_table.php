<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('attachment_name');
        });

        DB::table('speakers')
            ->whereNotNull('attachment_path')
            ->orderBy('id')
            ->chunkById(100, function ($speakers): void {
                foreach ($speakers as $speaker) {
                    DB::table('speakers')
                        ->where('id', $speaker->id)
                        ->update([
                            'attachments' => json_encode([[
                                'path' => $speaker->attachment_path,
                                'name' => $speaker->attachment_name ?: basename($speaker->attachment_path),
                                'size' => null,
                            ]], JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
