<?php

declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$publicPath = realpath(__DIR__ . '/public') ?: __DIR__ . '/public';
$storagePublicPath = realpath(__DIR__ . '/storage/app/public');

$normalize = static function (string $path): string {
    $resolvedPath = realpath($path);
    $path = $resolvedPath !== false ? $resolvedPath : $path;

    return rtrim($path, DIRECTORY_SEPARATOR);
};

$isWithin = static function (?string $path, ?string $basePath) use ($normalize): bool {
    if ($path === null || $path === '' || $basePath === null || $basePath === '') {
        return false;
    }

    $normalizedPath = $normalize($path);
    $normalizedBasePath = $normalize($basePath);

    return $normalizedPath === $normalizedBasePath || str_starts_with($normalizedPath, $normalizedBasePath . DIRECTORY_SEPARATOR);
};

if ($uri !== '/') {
    $candidatePath = null;

    if (str_starts_with($uri, '/storage/') && $storagePublicPath !== false) {
        $storageRelativePath = ltrim(substr($uri, strlen('/storage/')), '/');
        $candidatePath = realpath($storagePublicPath . DIRECTORY_SEPARATOR . $storageRelativePath);
    }

    if ($candidatePath === null || $candidatePath === false) {
        $candidatePath = realpath($publicPath . DIRECTORY_SEPARATOR . ltrim($uri, '/'));
    }

    $isInsidePublic = $candidatePath !== false && $isWithin($candidatePath, $publicPath);
    $isInsideStoragePublic = $candidatePath !== false && $storagePublicPath !== false && $isWithin($candidatePath, $storagePublicPath);

    if ($candidatePath !== false && is_file($candidatePath) && ($isInsidePublic || $isInsideStoragePublic)) {
        $extension = strtolower(pathinfo($candidatePath, PATHINFO_EXTENSION));
        $mimeTypeMap = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'mjs' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'map' => 'application/json; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'eot' => 'application/vnd.ms-fontobject',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'ico' => 'image/x-icon',
            'html' => 'text/html; charset=UTF-8',
            'txt' => 'text/plain; charset=UTF-8',
        ];
        $mimeType = $mimeTypeMap[$extension] ?? 'application/octet-stream';
        $lastModified = filemtime($candidatePath) ?: time();

        header('Content-Type: ' . $mimeType);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        header('X-Content-Type-Options: nosniff');

        $isVersionedBuildAsset = str_starts_with($uri, '/build/assets/');
        $isFontAsset = str_starts_with($uri, '/fonts/');
        $isStaticImage = str_starts_with($uri, '/img/');
        $isStorageAsset = str_starts_with($uri, '/storage/');

        if ($isVersionedBuildAsset || $isFontAsset || $isStaticImage) {
            header('Cache-Control: public, max-age=31536000, immutable');
        } elseif ($isStorageAsset) {
            header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        } else {
            header('Cache-Control: public, max-age=86400');
        }

        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? null;
        if ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified) {
            http_response_code(304);
            return true;
        }

        $compressibleExtensions = ['css', 'js', 'json', 'svg', 'txt', 'xml'];
        $supportsGzip = str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip');

        if ($supportsGzip && in_array($extension, $compressibleExtensions, true)) {
            header('Vary: Accept-Encoding');
            ob_start('ob_gzhandler');
            readfile($candidatePath);
            ob_end_flush();
            return true;
        }

        readfile($candidatePath);
        return true;
    }
}

require __DIR__ . '/public/index.php';
