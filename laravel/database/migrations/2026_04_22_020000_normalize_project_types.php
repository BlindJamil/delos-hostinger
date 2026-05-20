<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('projects')
            ->whereNotNull('type')
            ->orderBy('id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $normalized = strtolower(trim((string) preg_replace('/\s+/', ' ', $row->type)));
                    if ($normalized !== $row->type) {
                        DB::table('projects')->where('id', $row->id)->update(['type' => $normalized]);
                    }
                }
            });
    }

    public function down(): void
    {
        // No-op — normalization is non-destructive and should not be reverted.
    }
};
