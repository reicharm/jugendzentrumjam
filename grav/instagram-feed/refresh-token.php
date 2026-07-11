<?php

declare(strict_types=1);

/**
 * Erneuert das langlebige Instagram-Access-Token, BEVOR es nach 60 Tagen
 * abläuft. Meta erlaubt eine Erneuerung erst, wenn das Token mindestens 24h
 * alt ist - am besten alle 30-45 Tage ausführen, nicht kurz vor Ablauf.
 *
 * Aufruf per Cron-Job (empfohlen, falls SSH/Cron im Hosting verfügbar):
 *   php /pfad/zu/instagram-feed/refresh-token.php
 *
 * Oder per Browser-/Uptime-Monitor-Aufruf, falls kein Cron verfügbar ist:
 *   https://eure-domain.at/instagram-feed/refresh-token.php?key=EUER_GEHEIMWORT
 * (Geheimwort in config.php unter 'refresh_secret' festlegen)
 *
 * WICHTIG: Der genaue Endpunkt/Parametername hängt davon ab, welchen Login-
 * Flow ihr in Teil A des Setups verwendet habt (Meta ändert das gelegentlich).
 * Vor dem produktiven Cron-Einsatz einmal manuell ausführen und die Ausgabe
 * gegen die aktuelle Meta-Doku zur "Long-Lived Access Token"-Erneuerung
 * prüfen: https://developers.facebook.com/docs/instagram-platform
 */

$configFile = __DIR__ . '/config.php';

if (!is_file($configFile)) {
    fwrite(STDERR, "config.php fehlt.\n");
    exit(1);
}

$isCli = PHP_SAPI === 'cli';

$config = require $configFile;

if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
    $providedKey = $_GET['key'] ?? '';
    if (empty($config['refresh_secret']) || !hash_equals((string) $config['refresh_secret'], (string) $providedKey)) {
        http_response_code(403);
        echo "Zugriff verweigert.\n";
        exit;
    }
}

if (empty($config['access_token'])) {
    $message = "Kein Access Token in config.php hinterlegt.\n";
    $isCli ? fwrite(STDERR, $message) : print($message);
    exit(1);
}

$url = sprintf(
    'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=%s',
    rawurlencode((string) $config['access_token'])
);

$context = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]]);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    $message = "Token-Erneuerung fehlgeschlagen: keine Antwort von Instagram.\n";
    $isCli ? fwrite(STDERR, $message) : print($message);
    exit(1);
}

$data = json_decode($response, true);

if (!isset($data['access_token'])) {
    $message = "Token-Erneuerung fehlgeschlagen. Antwort: {$response}\n";
    $isCli ? fwrite(STDERR, $message) : print($message);
    exit(1);
}

$updatedConfig = array_merge($config, ['access_token' => $data['access_token']]);

$newConfigContent = "<?php\n\nreturn " . var_export($updatedConfig, true) . ";\n";
file_put_contents($configFile, $newConfigContent);

$expiresIn = $data['expires_in'] ?? '?';
echo "Token erfolgreich erneuert. Gültig für weitere {$expiresIn} Sekunden.\n";
