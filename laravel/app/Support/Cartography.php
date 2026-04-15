<?php

namespace App\Support;

/**
 * Geographic → SVG coordinate projection for the Iraq map.
 *
 * Iraq spans roughly:
 *   Longitude  38.5°E → 48.8°E (10.3° span)
 *   Latitude   29.0°N → 37.5°N (8.5° span)
 *
 * The SVG viewBox is 1000 × 800 units, chosen so that Iraq's shape
 * renders naturally (wider than tall). A simple linear projection is
 * used — at this geographic scale the Mercator distortion is visually
 * indistinguishable and the math stays trivially auditable.
 */
class Cartography
{
    /** Longitude bounds of the map viewBox, in decimal degrees east. */
    public const LNG_MIN = 38.5;
    public const LNG_MAX = 48.8;

    /** Latitude bounds of the map viewBox, in decimal degrees north. */
    public const LAT_MIN = 29.0;
    public const LAT_MAX = 37.5;

    /** SVG viewBox dimensions. Must match the iraq-map.svg viewBox. */
    public const VIEWBOX_WIDTH = 1000;
    public const VIEWBOX_HEIGHT = 800;

    /**
     * Project a geographic point to SVG viewBox coordinates.
     *
     * @param  float|null  $lat  Latitude in decimal degrees (north).
     * @param  float|null  $lng  Longitude in decimal degrees (east).
     * @return array{x: float, y: float}|null  null when either coordinate is missing.
     */
    public static function projectLatLng(?float $lat, ?float $lng): ?array
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        $lngSpan = self::LNG_MAX - self::LNG_MIN;
        $latSpan = self::LAT_MAX - self::LAT_MIN;

        $x = (($lng - self::LNG_MIN) / $lngSpan) * self::VIEWBOX_WIDTH;
        // SVG y-axis is inverted (0 at top), so flip via (height - ...)
        $y = self::VIEWBOX_HEIGHT - (($lat - self::LAT_MIN) / $latSpan) * self::VIEWBOX_HEIGHT;

        return [
            'x' => round($x, 2),
            'y' => round($y, 2),
        ];
    }
}
