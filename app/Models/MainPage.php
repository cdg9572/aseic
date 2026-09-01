<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'is_visible',
        'folder_name',
        'event_name',
        'event_start_date',
        'event_end_date',
        'use_custom_event_date',
        'event_date_text',
        'banner_id',
        'popup_id',
        'programme_background_path',
        'programme_background_name',
        'programme_items',
        'register_background_path',
        'register_background_name',
        'past_forum_video_url',
        'host_images',
        'organizer_images',
        'co_organizer_images',
        'footer_text',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'event_start_date' => 'date',
            'event_end_date' => 'date',
            'use_custom_event_date' => 'boolean',
            'programme_items' => 'array',
            'host_images' => 'array',
            'organizer_images' => 'array',
            'co_organizer_images' => 'array',
        ];
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'main_page_speaker')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function links(): HasMany
    {
        return $this->hasMany(MainPageLink::class);
    }

    /**
     * @return array<int, array{path: string, name: string, size: int|null}>
     */
    public function getHostImageFilesAttribute(): array
    {
        return $this->normalizedFiles($this->host_images);
    }

    /**
     * @return array<int, array{path: string, name: string, size: int|null}>
     */
    public function getOrganizerImageFilesAttribute(): array
    {
        return $this->normalizedFiles($this->organizer_images);
    }

    /**
     * @return array<int, array{path: string, name: string, size: int|null}>
     */
    public function getCoOrganizerImageFilesAttribute(): array
    {
        return $this->normalizedFiles($this->co_organizer_images);
    }

    /**
     * @return array<int, array{time: string, subject: string, content: string}>
     */
    public function getProgrammeItemListAttribute(): array
    {
        if (! is_array($this->programme_items)) {
            return [];
        }

        return array_values(array_filter(array_map(static function (mixed $item): array {
            $item = is_array($item) ? $item : [];

            return [
                'time' => trim((string) ($item['time'] ?? '')),
                'subject' => trim((string) ($item['subject'] ?? '')),
                'content' => trim((string) ($item['content'] ?? '')),
            ];
        }, $this->programme_items), static fn (array $item): bool => implode('', $item) !== ''));
    }

    public function getEventDateDisplayAttribute(): string
    {
        if ($this->use_custom_event_date) {
            return trim((string) $this->event_date_text);
        }

        if (! $this->event_start_date) {
            return '';
        }

        $startDate = $this->event_start_date->format('Y. m. d');
        if (! $this->event_end_date || $this->event_start_date->isSameDay($this->event_end_date)) {
            return $startDate;
        }

        return $startDate.' – '.$this->event_end_date->format('Y. m. d');
    }

    /**
     * @return array<int, array{path: string, name: string, size: int|null}>
     */
    private function normalizedFiles(mixed $files): array
    {
        if (! is_array($files)) {
            return [];
        }

        return array_values(array_filter($files, static function (mixed $file): bool {
            return is_array($file) && isset($file['path'], $file['name']);
        }));
    }
}
