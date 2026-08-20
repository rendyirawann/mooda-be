<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Tabel `menu_addons` (DB bersama). */
class MenuAddon extends Model
{
    protected $table = 'menu_addons';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
