<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tabel `categories` (DB bersama). */
class Category extends Model
{
    protected $table = 'categories';

    protected $guarded = [];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
