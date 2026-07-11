<?php

declare(strict_types=1);

/**
 * Liest die letzten Instagram-Beiträge/Reels über die offizielle Meta Graph
 * API und liefert sie als JSON aus. Cached serverseitig, damit nicht bei
 * jedem Seitenaufruf ein Live-Call gegen Instagram passiert (Rate-Limits,
 * Ladezeit) und das Access Token nie an den Browser geht.
 *
 * Aufgerufen vom jam-theme (siehe templates/partials/instagram.html.twig)
 * per fetch() aus dem Browser, relativ zur Domain: /instagram-feed/fetch.php
 */

header('Content-Type: application/json; charset=utf-8');

$configFile = __DIR__ . '/config.php';
$cacheFile = __DIR__ . '/cache.json';

function respond_with_error(string $message, int $status, ?string $cacheFile): void
{
    // Bei einem Fehler lieber den letzten bekannten Stand zeigen als gar nichts,
    // falls ein Cache existiert (z.B. Instagram kurz nicht erreichbar).
    if ($cacheFile !== null && is_file($cacheFile)) {
        header('Cache-Control: public, max-age=300');
        readfile($cacheFile);
        exit;
    }
    http_response_code($status);
    echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!is_file($configFile)) {
    respond_with_error(
        'Instagram-Feed ist noch nicht konfiguriert (config.php fehlt, siehe config.sample.php).',
        500,
        null
    );
}

$config = require $configFile;

$cacheTtl = (int) ($config['cache_ttl_seconds'] ?? 21600);
$limit = (int) ($config['post_count'] ?? 6);

if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTtl) {
    header('Cache-Control: public, max-age=' . $cacheTtl);
    readfile($cacheFile);
    exit;
}

if (empty($config['ig_user_id']) || empty($config['access_token'])) {
    respond_with_error('Instagram-Feed ist unvollständig konfiguriert.', 500, $cacheFile);
}

$fields = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp';
$url = sprintf(
    'https://graph.facebook.com/v21.0/%s/media?fields=%s&limit=%d&access_token=%s',
    rawurlencode((string) $config['ig_user_id']),
    rawurlencode($fields),
    $limit,
    rawurlencode((string) $config['access_token'])
);

$context = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    respond_with_error('Instagram-Feed konnte nicht geladen werden.', 502, $cacheFile);
}

$data = json_decode($response, true);

if (!isset($data['data']) || !is_array($data['data'])) {
    // Häufigste Ursache: abgelaufenes Access Token -> siehe refresh-token.php
    respond_with_error('Instagram-API-Fehler, Token evtl. abgelaufen.', 502, $cacheFile);
}

$items = array_map(static function (array $item): array {
    $isVideo = ($item['media_type'] ?? 'IMAGE') === 'VIDEO';
    return [
        'id' => $item['id'] ?? null,
        'caption' => isset($item['caption']) ? mb_substr((string) $item['caption'], 0, 140) : '',
        'type' => $item['media_type'] ?? 'IMAGE',
        'image' => $isVideo ? ($item['thumbnail_url'] ?? $item['media_url'] ?? null) : ($item['media_url'] ?? null),
        'permalink' => $item['permalink'] ?? '#',
        'timestamp' => $item['timestamp'] ?? null,
    ];
}, $data['data']);

$output = json_encode(['items' => $items], JSON_UNESCAPED_UNICODE);
file_put_contents($cacheFile, $output);

header('Cache-Control: public, max-age=' . $cacheTtl);
echo $output;
