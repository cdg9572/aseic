<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressBookContact extends Model
{
    protected $fillable = ['address_book_id', 'name', 'email', 'is_subscribed', 'created_at'];

    protected function casts(): array
    {
        return ['is_subscribed' => 'boolean'];
    }

    public function addressBook(): BelongsTo
    {
        return $this->belongsTo(AddressBook::class);
    }
}
