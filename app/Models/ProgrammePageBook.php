<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammePageBook extends Model
{
    protected $fillable = [
        'programme_page_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'link',
        'sort_order',
    ];

    public function programmePage(): BelongsTo
    {
        return $this->belongsTo(ProgrammePage::class);
    }
}
