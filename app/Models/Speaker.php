<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speaker extends Model
{
    use HasFactory, SoftDeletes;

    public const ROLE_SPEAKER = 'speaker';

    public const ROLE_MODERATOR = 'moderator';

    public const ROLE_PANEL = 'panel';

    public const ROLE_STARTUP = 'startup';

    protected $fillable = [
        'first_name',
        'last_name',
        'position',
        'affiliation',
        'presentation_subject',
        'profile_image',
        'profile_image_name',
        'role',
        'is_active',
        'is_image_visible',
        'content',
        'attachment_path',
        'attachment_name',
        'attachments',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_image_visible' => 'boolean',
            'attachments' => 'array',
        ];
    }

    /**
     * @return array<int, array{path: string, name: string, size: int|null}>
     */
    public function getAttachmentFilesAttribute(): array
    {
        if (is_array($this->attachments)) {
            return $this->attachments;
        }

        if ($this->attachment_path) {
            return [[
                'path' => $this->attachment_path,
                'name' => $this->attachment_name ?: basename($this->attachment_path),
                'size' => null,
            ]];
        }

        return [];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_SPEAKER => 'SPEAKER',
            self::ROLE_MODERATOR => 'MODERATOR',
            self::ROLE_PANEL => 'PANEL',
            self::ROLE_STARTUP => 'START UP',
        ];
    }
}
