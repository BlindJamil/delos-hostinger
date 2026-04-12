<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LangFileParityTest extends TestCase
{
    /**
     * Recursive key-walk that builds a flat list of dotted paths for an array.
     * Used to compare translation file shapes across locales.
     */
    private function flatten(array $data, string $prefix = ''): array
    {
        $keys = [];
        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                // For numerically-indexed arrays (lists), we only care that the
                // list exists and has the same count — not the individual item
                // shapes, since list items are data, not keyed translations.
                if (array_is_list($value)) {
                    $keys[] = $path . '[]:' . count($value);
                } else {
                    $keys = array_merge($keys, $this->flatten($value, $path));
                }
            } else {
                $keys[] = $path;
            }
        }
        return $keys;
    }

    /**
     * @return array<string, array{string}>
     */
    public static function langFileProvider(): array
    {
        return [
            'common' => ['common'],
            'seo' => ['seo'],
            'home' => ['home'],
            'about' => ['about'],
            'brands' => ['brands'],
            'services' => ['services'],
            'projects' => ['projects'],
            'branches' => ['branches'],
            'contact' => ['contact'],
        ];
    }

    #[DataProvider('langFileProvider')]
    public function test_all_locales_have_identical_key_structure(string $file): void
    {
        $langPath = base_path('lang');

        $enKeys = $this->flatten(require "{$langPath}/en/{$file}.php");
        $arKeys = $this->flatten(require "{$langPath}/ar/{$file}.php");
        $itKeys = $this->flatten(require "{$langPath}/it/{$file}.php");

        sort($enKeys);
        sort($arKeys);
        sort($itKeys);

        $this->assertSame(
            $enKeys,
            $arKeys,
            "EN and AR key structures diverge in lang/{$file}.php. Missing or extra keys:\n"
            . implode("\n  - ", array_merge(
                array_map(fn ($k) => "AR missing: {$k}", array_diff($enKeys, $arKeys)),
                array_map(fn ($k) => "AR extra: {$k}", array_diff($arKeys, $enKeys))
            ))
        );

        $this->assertSame(
            $enKeys,
            $itKeys,
            "EN and IT key structures diverge in lang/{$file}.php. Missing or extra keys:\n"
            . implode("\n  - ", array_merge(
                array_map(fn ($k) => "IT missing: {$k}", array_diff($enKeys, $itKeys)),
                array_map(fn ($k) => "IT extra: {$k}", array_diff($itKeys, $enKeys))
            ))
        );
    }

    public function test_services_items_slug_order_matches_across_locales(): void
    {
        $langPath = base_path('lang');

        $enSlugs = array_keys((require "{$langPath}/en/services.php")['items']);
        $arSlugs = array_keys((require "{$langPath}/ar/services.php")['items']);
        $itSlugs = array_keys((require "{$langPath}/it/services.php")['items']);

        $this->assertSame($enSlugs, $arSlugs, 'Services item slug order differs between EN and AR');
        $this->assertSame($enSlugs, $itSlugs, 'Services item slug order differs between EN and IT');
    }
}
