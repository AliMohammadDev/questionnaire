<?php

namespace App\Support;

class SeedPhotoCatalog
{
    /** @var array<string, int> */
    private const Photos = [
        'architecture' => 323780,
        'buildings' => 439391,
        'graphic' => 38840594,
        'web' => 196644,
        'residential' => 1571460,
        'commercial' => 260922,
        'industrial' => 12381213,
        'branding' => 6648412,
        'printing' => 4246242,
        'marketing' => 590022,
        'ecommerce' => 5632402,
        'services' => 3184454,
        'modern' => 1643383,
        'classic' => 30475008,
        'islamic' => 34832416,
        'minimalist' => 30211360,
        'scandinavian' => 1090638,
        'bohemian' => 6750181,
        'office' => 380768,
        'colorful' => 9282318,
    ];

    public static function path(string $key): string
    {
        return database_path('seeders/images/'.$key.'.jpg');
    }

    public static function sourceUrl(string $key): string
    {
        return 'https://www.pexels.com/photo/'.self::Photos[$key].'/';
    }
}
