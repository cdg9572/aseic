<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaContent extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_PHOTO_FOLDER = 'photo_folder';

    public const TYPE_PHOTO_ITEM = 'photo_item';

    public const TYPE_NEWS_FOLDER = 'news_folder';

    public const TYPE_NEWS_ITEM = 'news_item';

    public const TYPE_YOUTUBE = 'youtube';

    protected $fillable = [
        'type',
        'parent_id',
        'category_id',
        'page_title',
        'subtitle',
        'title',
        'content',
        'published_date',
        'view_count',
        'link',
        'image_path',
        'image_name',
        'image_size',
        'is_visible',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'view_count' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderByDesc('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
