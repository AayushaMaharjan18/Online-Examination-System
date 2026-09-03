<?php

namespace App\Support;

class MediaUrl
{
    /**
     * Convert a stored media value into an absolute, browser-ready URL.
     *
     * FileUpload saves the relative path on the configured disk (e.g.
     * "products/abc.png"), while legacy records may already contain a full
     * URL. This returns full URLs for both cases so the storefront can use
     * the value directly in an <img src>.
     */
    public static function for(string|null $path): ?string
    {
        if (! $path) {
            return null;
        }

        // Already an absolute URL? Return it untouched.
        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
            return $path;
        }

        return url('storage/'.ltrim($path, '/'));
    }

    /**
     * Map a collection of stored paths (e.g. the product "images" JSON
     * column) to absolute URLs, dropping any empty values.
     *
     * @param  array<mixed>|null  $paths
     * @return array<int, string>
     */
    public static function many(array|null $paths): array
    {
        if (! $paths) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (mixed $path): ?string => is_string($path) ? self::for($path) : null,
            $paths
        )));
    }
}