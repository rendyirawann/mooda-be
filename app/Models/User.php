<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Memetakan tabel `users` milik stakko-pos (DB bersama).
 * PK = UUID (kolom id bertipe uuid), tenant_id = bigint.
 * Tidak memakai global tenant-scope stakko; scoping dilakukan eksplisit di controller.
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    // id bertipe uuid (bukan auto-increment).
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id', 'name', 'username', 'email', 'phone', 'no_wa',
        'is_active', 'password', 'email_verified_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_login' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** Boleh memakai aplikasi mobile? aktif & tidak diblokir. */
    public function canUseApp(): bool
    {
        return $this->is_active !== false && $this->banned_at === null;
    }
}
