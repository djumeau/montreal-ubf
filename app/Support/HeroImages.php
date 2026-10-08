<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Images of the home page hero, kept in private storage (storage/app/private/home) and served by the private.home route.
 * A slide is a pair of files sharing a name: "montreal-sunset-desktop.jpg" and "montreal-sunset-mobile.jpg".
 * No table: the folder is the list, so files copied there by hand show up too.
 */
class HeroImages
{
    public const DISK = 'private';
    public const DIRECTORY = 'home';

    // File name endings, one image of each per slide
    public const TYPES = ['desktop', 'mobile'];

    public const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public const MAX_KILOBYTES = 5120; // 5 MB, unless the server accepts less (see maxKilobytes)

    /**
     * Slides of the folder, newest first: ['slug', 'complete', 'uploaded_at', 'desktop' => image|null, 'mobile' => image|null],
     * an image being ['name', 'url', 'uploaded_at'].
     */
    public static function all(): Collection
    {
        $disk = Storage::disk(self::DISK);
        $slides = [];

        foreach ($disk->files(self::DIRECTORY) as $path) {
            $name = basename($path);

            if (!$parts = self::parse($name)) {
                continue; // Not named "...-desktop" / "...-mobile", or not an image
            }

            $modified = $disk->lastModified($path);

            $slides[$parts['slug']][$parts['type']] = [
                'name' => $name,
                'url' => route('private.home', $name) . '?v=' . $modified, // A replaced image is not shown from the browser's cache
                'uploaded_at' => Carbon::createFromTimestamp($modified, config('app.timezone')),
            ];
        }

        return collect($slides)
            ->map(fn (array $images, string $slug) => [
                'slug' => $slug,
                'desktop' => $images['desktop'] ?? null,
                'mobile' => $images['mobile'] ?? null,
                'complete' => count($images) === count(self::TYPES),
                'uploaded_at' => collect($images)->max('uploaded_at'),
            ])
            ->sortByDesc('uploaded_at')
            ->values();
    }

    /**
     * "montreal-sunset-desktop.jpg" gives ['slug' => 'montreal-sunset', 'type' => 'desktop']; null for any other file.
     */
    public static function parse(string $name): ?array
    {
        $pattern = '/^([a-z0-9][a-z0-9-]*)-(' . implode('|', self::TYPES) . ')\.(' . implode('|', self::EXTENSIONS) . ')$/i';

        return preg_match($pattern, $name, $matches)
            ? ['slug' => strtolower($matches[1]), 'type' => strtolower($matches[2])]
            : null;
    }

    /**
     * Files of a slide: both types, or only the one given.
     */
    public static function files(string $slug, ?string $type = null): array
    {
        return array_values(array_filter(
            Storage::disk(self::DISK)->files(self::DIRECTORY),
            function (string $path) use ($slug, $type) {
                $parts = self::parse(basename($path));

                return $parts && $parts['slug'] === $slug && ($type === null || $parts['type'] === $type);
            },
        ));
    }

    /**
     * Largest image accepted, in kilobytes: PHP's own upload limit when it is lower than ours.
     */
    public static function maxKilobytes(): int
    {
        return (int) min(self::MAX_KILOBYTES, self::iniKilobytes('upload_max_filesize'));
    }

    // "2M" -> 2048; no limit (0 or unset) counts as ours
    private static function iniKilobytes(string $setting): int
    {
        $value = trim((string) ini_get($setting));
        $number = (float) $value;

        if ($number <= 0) {
            return self::MAX_KILOBYTES;
        }

        return (int) match (strtoupper(substr($value, -1))) {
            'G' => $number * 1024 * 1024,
            'M' => $number * 1024,
            'K' => $number,
            default => $number / 1024, // Bytes
        };
    }
}
