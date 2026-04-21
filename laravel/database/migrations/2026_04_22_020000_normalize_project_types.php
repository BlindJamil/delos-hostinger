<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            UPDATE projects
               SET type = LOWER(TRIM(REGEXP_REPLACE(type, '\\s+', ' ', 'g')))
             WHERE type IS NOT NULL
               AND type <> LOWER(TRIM(REGEXP_REPLACE(type, '\\s+', ' ', 'g')))
        ");
    }

    public function down(): void
    {
        // No-op — normalization is non-destructive and should not be reverted.
    }
};
