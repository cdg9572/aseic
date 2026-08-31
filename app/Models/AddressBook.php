<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressBook extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'created_by', 'updated_by'];

    public function contacts(): HasMany
    {
        return $this->hasMany(AddressBookContact::class)->latest('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
