<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * mooda-be BERBAGI database dengan stakko-pos — akun & data berasal dari sana.
     * Sengaja dikosongkan agar `db:seed` tidak pernah menulis ke DB bersama.
     */
    public function run(): void
    {
        // no-op (jangan seed ke DB stakko_pos).
    }
}
