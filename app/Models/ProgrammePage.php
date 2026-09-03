<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgrammePage extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_THEME = 'theme';

    public const TYPE_PROGRAMME = 'programme';

    public const TYPE_SPEAKERS = 'speakers';

    public const TYPE_BOOK = 'book';

    public const TYPE_ARCHIVE_THEME = 'archive_theme';

    public const TYPE_ARCHIVE_PROGRAMME = 'archive_programme';

    public const TYPE_ARCHIVE_SPEAKERS = 'archive_speakers';

    public const TYPE_ARCHIVE_LEGACY = 'archive_legacy';

    protected $fillable = [
        'type',
        'category_id',
        'page_title',
        'subtitle',
        'title',
        'location',
        'event_date',
        'content',
        'book_title',
        'book_file_path',
        'book_file_name',
        'book_file_size',
        'book_link',
        'created_by',
        'updated_by',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(ProgrammePageSession::class)->orderBy('sort_order');
    }

    public function books(): HasMany
    {
        return $this->hasMany(ProgrammePageBook::class)->orderBy('sort_order');
    }

    public function mainPageLink(): MorphOne
    {
        return $this->morphOne(MainPageLink::class, 'linkable');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return array<string, string> */
    public static function typeLabels(): array
    {
        return [
            self::TYPE_THEME => 'Theme',
            self::TYPE_PROGRAMME => 'Programme',
            self::TYPE_SPEAKERS => 'Speakers',
            self::TYPE_BOOK => 'Programme Book',
            self::TYPE_ARCHIVE_THEME => 'Past Forums (2025~) - Theme',
            self::TYPE_ARCHIVE_PROGRAMME => 'Past Forums (2025~) - Programme',
            self::TYPE_ARCHIVE_SPEAKERS => 'Past Forums (2025~) - Speakers',
            self::TYPE_ARCHIVE_LEGACY => 'Past Forums (2015~2024)',
        ];
    }
}
