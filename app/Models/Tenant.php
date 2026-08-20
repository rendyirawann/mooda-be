<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Tabel `tenants` (DB bersama). PK bigint; `uuid` kolom terpisah. */
class Tenant extends Model
{
    protected $table = 'tenants';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deposit_points' => 'decimal:2',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }
}
