<?php
// Kopieren nach config.php (gleicher Ordner) und mit euren echten Werten befüllen.
// config.php NICHT ins Git-Repo committen (steht in .gitignore) - enthält ein
// geheimes Zugriffstoken.
//
// Woher die Werte kommen: siehe grav/README.md, Abschnitt "Instagram-Feed",
// Teil A (Meta-Entwicklerkonto einrichten).

return [
    // Instagram Business-Account-ID (nicht der @handle, sondern eine Zahl),
    // ermittelt über /{page-id}?fields=instagram_business_account
    'ig_user_id' => 'HIER_IG_USER_ID_EINTRAGEN',

    // Langlebiges Access Token (60 Tage gültig)
    'access_token' => 'HIER_ACCESS_TOKEN_EINTRAGEN',

    // Frei wählbares Geheimwort, um refresh-token.php auch per Browser-Aufruf
    // (ohne Cron/SSH) sicher auslösen zu können. Irgendeine lange Zufallszeichenkette.
    'refresh_secret' => 'HIER_EIGENES_GEHEIMWORT_EINTRAGEN',

    // Wie viele Beiträge/Reels angezeigt werden sollen
    'post_count' => 6,

    // Wie lange der Feed serverseitig zwischengespeichert wird (Sekunden),
    // bevor er neu von Instagram geladen wird. Schont das Rate-Limit der API.
    'cache_ttl_seconds' => 21600, // 6 Stunden
];
