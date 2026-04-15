<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Canonical english key (used for CSS classes, deep-linking, etc.)
            // e.g. "erbil", "baghdad" — kept independent of the translated names
            // so renaming a branch in the admin doesn't break anchor links.
            $table->string('city_key')->unique();

            // Translated city name shown in the heading of each card
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('name_it')->nullable();

            // Full address line — translated (Arabic writes directions differently)
            $table->string('address_en')->nullable();
            $table->string('address_ar')->nullable();
            $table->string('address_it')->nullable();

            // Opening hours — translated (day abbreviations differ: Sat/Sab/السبت)
            $table->string('hours_en')->nullable();
            $table->string('hours_ar')->nullable();
            $table->string('hours_it')->nullable();

            // Established year — translated so "Est. 2020" / "تأسّس 2020" / "Dal 2020"
            // can be edited verbatim per language.
            $table->string('established_en')->nullable();
            $table->string('established_ar')->nullable();
            $table->string('established_it')->nullable();

            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            // Geographic position — used both to pin on the Iraq SVG map and
            // to build the Get Directions URL (geo:lat,lng / maps.google).
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Optional override — if set, the Get Directions button uses this
            // URL verbatim instead of auto-generating from lat/lng. Useful if
            // the showroom has a curated Google Maps listing.
            $table->string('directions_url')->nullable();

            // Showroom photo — nullable because real photos arrive later.
            // When null, the public card uses the letter-watermark design.
            $table->string('image')->nullable();

            // Flagship = bigger pin on the map + flagship badge on the card.
            // Only one is expected (Erbil, as HQ) but we don't enforce uniqueness
            // so the admin can change it without a constraint fight.
            $table->boolean('is_flagship')->default(false);

            $table->integer('sort_order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
