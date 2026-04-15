<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();

            // The dotted lang-file key this row overrides, e.g. "home.hero.heading_accent".
            // Unique lookup column — this is the hot path for pcontent($key).
            $table->string('key', 191)->unique();

            // Admin-side grouping (not used for lookups, just for the editor UI).
            // e.g. page = "home", section = "hero".
            $table->string('page', 40)->index();
            $table->string('section', 40)->nullable();
            $table->integer('sort_order')->default(0);

            // Content type drives which input the admin editor renders:
            //   text     — single-line input
            //   textarea — multi-line plain
            //   rich     — TipTap HTML
            //   url      — external URL (validated)
            //   image    — upload → stores "uploads/page-content/xxx.jpg"
            //   video    — upload → stores "uploads/page-content/xxx.mp4"
            //             (or external URL for Vimeo/YouTube — free-form)
            $table->string('type', 20)->default('text');

            // Multilingual values. Text/textarea/rich hold the string per locale.
            // Image/video typically store the SAME path across all three locales
            // (a single media asset per key), but we keep the per-locale columns
            // to allow locale-specific imagery if ever needed.
            $table->text('value_en')->nullable();
            $table->text('value_ar')->nullable();
            $table->text('value_it')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
