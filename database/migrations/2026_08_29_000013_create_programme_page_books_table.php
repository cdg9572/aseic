<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programme_page_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_page_id')->constrained('programme_pages')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('link', 2048)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['programme_page_id', 'sort_order']);
        });

        DB::table('programme_pages')
            ->where('type', 'book')
            ->where(function ($query): void {
                $query->whereNotNull('book_title')
                    ->orWhereNotNull('book_file_path')
                    ->orWhereNotNull('book_link');
            })
            ->orderBy('id')
            ->chunkById(100, function ($pages): void {
                $rows = [];

                foreach ($pages as $page) {
                    $rows[] = [
                        'programme_page_id' => $page->id,
                        'title' => $page->book_title,
                        'file_path' => $page->book_file_path,
                        'file_name' => $page->book_file_name,
                        'file_size' => $page->book_file_size,
                        'link' => $page->book_link,
                        'sort_order' => 1,
                        'created_at' => $page->created_at,
                        'updated_at' => $page->updated_at,
                    ];
                }

                if ($rows !== []) {
                    DB::table('programme_page_books')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('programme_page_books');
    }
};
