<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tabel `menus` (DB bersama). PK bigint; `uuid` kolom terpisah. */
class Menu extends Model
{
    protected $table = 'menus';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'discount_percent' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(MenuAddon::class);
    }

    public function activeAddons(): HasMany
    {
        return $this->hasMany(MenuAddon::class)->where('is_active', true);
    }

    /** Harga setelah diskon (server-side authoritative). */
    public function getFinalPriceAttribute(): float
    {
        $p = (float) $this->price;
        $d = (int) ($this->discount_percent ?? 0);

        return $d > 0 ? round($p - ($p * $d / 100), 2) : $p;
    }
}
