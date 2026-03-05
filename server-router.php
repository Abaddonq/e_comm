<?php

declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$publicPath = __DIR__ . '/public';

if ($uri !== '/') {
    $candidatePath = realpath($publicPath . DIRECTORY_SEPARATOR . ltrim($uri, '/'));

    if ($candidatePath !== false && str_starts_with($candidatePath, $publicPath) && is_file($candidatePath)) {
        $extension = strtolower(pathinfo($candidatePath, PATHINFO_EXTENSION));
        $mimeType = (string) (mime_content_type($candidatePath) ?: 'application/octet-stream');
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
