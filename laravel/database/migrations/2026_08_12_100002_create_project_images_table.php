<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gallery images for a project detail page. Columns mirror the hybrid-image
 * contract already used on projects/employees/etc. (image / image_mobile /
 * focal_point) so <x-responsive-image> renders them with zero special-casing.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->string('image_mobile')->nullable();
            $table->string('focal_point', 15)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Covers the only read pattern this table needs: a project's
            // images, in display order.
            $table->index(['project_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
