<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('boards')) {
            return;
        }

        $board = DB::table('boards')->where('slug', 'notices')->first();
        if (! $board) {
            return;
        }

        $fields = json_decode((string) $board->custom_fields_config, true);
        $fields = is_array($fields) ? $fields : [];

        foreach ($fields as $field) {
            if (($field['name'] ?? null) === 'subtitle') {
                return;
            }
        }

        array_unshift($fields, [
            'name' => 'subtitle',
            'type' => 'editor',
            'label' => 'Sub Title',
            'options' => null,
            'required' => false,
            'max_length' => null,
            'placeholder' => null,
        ]);

        DB::table('boards')->where('id', $board->id)->update([
            'custom_fields_config' => json_encode($fields, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('boards')) {
            return;
        }

        $board = DB::table('boards')->where('slug', 'notices')->first();
        if (! $board) {
            return;
        }

        $fields = json_decode((string) $board->custom_fields_config, true);
        $fields = is_array($fields) ? $fields : [];
        $fields = array_values(array_filter(
            $fields,
            static fn (array $field): bool => ($field['name'] ?? null) !== 'subtitle'
        ));

        DB::table('boards')->where('id', $board->id)->update([
            'custom_fields_config' => $fields === []
                ? null
                : json_encode($fields, JSON_UNESCAPED_UNICODE),
        ]);
    }
};
