# j@m Website – Grav CMS Setup

Dieses Verzeichnis enthält **nicht** das komplette Grav-System, sondern nur die
für j@m angepassten Teile (Theme `jam-theme`, Formulare/Blueprints und
Beispiel-Inhalte). Grav selbst wird als fertiges Release-Paket installiert.

## 1. Voraussetzungen auf eurem Webhosting

- PHP 8.1 oder neuer (bei den meisten aktuellen Hosting-Paketen Standard)
- **Keine Datenbank nötig**
- Zugriff per FTP/SFTP (reicht für den Betrieb völlig aus)

## 2. Grav installieren

1. Aktuelles "Grav Core"-Paket (ohne vorinstallierte Themes) von
   https://getgrav.org/downloads herunterladen.
2. Entpacken und den gesamten Ordnerinhalt per FTP in das Hauptverzeichnis
   eures Hostings hochladen (dort, wo eure Domain hinzeigt).
3. Aus diesem Repository den Ordner `user/themes/jam-theme` in
   `user/themes/jam-theme` auf dem Server hochladen (bzw. den bestehenden
   `user/themes`-Ordner damit ergänzen).
4. Aus diesem Repository `user/blueprints/pages/*` in den entsprechenden
   Ordner auf dem Server hochladen.
5. Optional: die Beispiel-Inhalte aus `user/pages/` mit hochladen, um direkt
   eine Startseite, "Neuigkeiten", "Termine" und "Über uns" zu haben – Texte
   und Beispiel-Einträge können danach im Admin-Panel angepasst/gelöscht
   werden.
6. Im Grav-Admin (siehe Punkt 3) unter **Themes** das Theme "jam-theme"
   aktivieren.

## 3. Admin-Panel installieren (für die Redakteur:innen)

Das Admin-Panel ist ein offizielles Grav-Plugin und wird einmalig über die
Kommandozeile (falls SSH verfügbar) oder per manuellem Upload installiert:

- Mit SSH-Zugriff: `bin/gpm install admin`
- Ohne SSH: Admin-Plugin-Paket von https://getgrav.org/downloads (Sektion
  "Plugins") laden und den Ordner `user/plugins/admin` per FTP hochladen.

Danach ist das Redaktions-Backend unter `eure-domain.at/admin` erreichbar.
Beim ersten Aufruf wird ein Administrator-Account angelegt.

## 4. Redakteur:innen-Accounts anlegen

Im Admin-Panel unter **Users**:

1. Neuen Benutzer anlegen (Name, E-Mail, Passwort).
2. Rolle/Access auf **Author** oder eine eigene Rolle "Redakteur" mit Rechten
   für `pages` (lesen/schreiben) beschränken – kein Zugriff auf
   System-/Plugin-Einstellungen nötig.

Jede Person kann sich danach eigenständig unter `/admin` anmelden.

## 5. Inhalte pflegen

- **Neue Neuigkeit (= Blog-Beitrag):** Im Admin-Panel unter "Neuigkeiten" →
  "Add Page" → Seitentyp **Neuigkeit** wählen → Titel, Text, **Titelbild**
  (wichtig – erscheint groß auf Startseite, Übersicht und im Beitrag),
  optional Verfasst-von-Name → Speichern.
- **Neuer Termin:** Unter "Termine" → "Add Page" → Seitentyp **Termin**
  wählen → Titel, Datum, Uhrzeit, Ort, Beschreibung → Speichern.
- Termine verschwinden automatisch aus der Übersicht "Anstehende Termine",
  sobald ihr Datum vergangen ist, und landen im ausklappbaren Bereich
  "Vergangene Termine".
- **Über uns pflegen:** Auf der Seite "Über uns" im Admin-Panel können
  beliebig viele Mitarbeitende (Name, Rolle, Foto) und Räume (Name,
  Beschreibung, Foto) als Liste hinzugefügt werden – erscheinen automatisch
  als Kacheln auf der Seite.

## 6. Instagram-Feed einbinden

Der Footer und die Startseite verlinken immer auf
`instagram.com/jamkremsmuenster` (Handle in `jam-theme.yaml` unter
`instagram_handle` änderbar). Für einen eingebetteten Live-Feed (nicht nur
einen Link) gibt es zwei Wege – **ohne einen von beiden funktioniert die
Seite trotzdem**, es wird dann nur der Folgen-Button angezeigt.

### Variante A: eigener Feed über die offizielle Meta Graph API (empfohlen, kein Drittanbieter)

**Teil 1 – Zugänge einrichten (einmalig, über euren Instagram-/Meta-Account):**

1. Instagram-Account auf **Professionell → Unternehmen** umstellen.
2. Mit einer Facebook-Seite verknüpfen (kann eine einfache, ungenutzte Seite sein).
3. Auf [developers.facebook.com](https://developers.facebook.com) ein
   Entwicklerkonto anlegen und eine neue App vom Typ **Unternehmen (Business)**
   erstellen.
4. Produkt **Instagram Graph API** zur App hinzufügen und dabei die
   Facebook-Seite/den Instagram-Account verbinden.
5. Euch selbst als App-Administrator/Tester eintragen.
6. Im **Graph API Explorer** ein Access Token mit der Berechtigung
   `instagram_basic` erzeugen und anschließend gegen ein **langlebiges Token**
   (60 Tage gültig) eintauschen.
7. Die Instagram-Business-Account-ID ermitteln (`/me/accounts`, dann
   `/{page-id}?fields=instagram_business_account`).

Meta ändert diese Klickpfade gelegentlich – bei Unklarheiten die aktuelle
Doku unter https://developers.facebook.com/docs/instagram-platform prüfen.

**Teil 2 – Website-Anbindung (bereits fertig in diesem Repo):**

1. Den Ordner `grav/instagram-feed/` aus diesem Repository so hochladen, dass
   er unter `eure-domain.at/instagram-feed/` erreichbar ist (also z.B. direkt
   ins Hosting-Hauptverzeichnis, so wie `index.php`).
2. Auf dem Server `instagram-feed/config.sample.php` zu `instagram-feed/config.php`
   kopieren (z.B. per FTP-Umbenennung) und die Werte aus Teil 1 eintragen
   (`ig_user_id`, `access_token`) sowie ein eigenes `refresh_secret` (beliebige
   lange Zeichenkette) festlegen.
3. Fertig – die Startseite lädt den Feed automatisch über
   `instagram-feed/fetch.php` (mit Server-seitigem Cache, Standard 6 Stunden).
4. **Token-Erneuerung nicht vergessen:** Das Token läuft nach 60 Tagen ab.
   `instagram-feed/refresh-token.php` erneuert es automatisch und schreibt das
   neue Token zurück in `config.php`. Am besten als Cron-Job alle 30–45 Tage
   einplanen (`php instagram-feed/refresh-token.php`), falls euer Hosting
   Cron/SSH bietet. Ohne Cron: die Datei mit `?key=EUER_REFRESH_SECRET` im
   Browser aufrufen (z.B. per Kalender-Erinnerung alle 6 Wochen, oder über
   einen kostenlosen Uptime-Monitor-Dienst, der die URL regelmäßig aufruft).

### Variante B: Drittanbieter-Feed-Widget (schneller eingerichtet)

1. Bei einem Anbieter wie [elfsight.com](https://elfsight.com) oder
   [snapwidget.com](https://snapwidget.com) einen kostenlosen "Instagram
   Feed"-Widget-Code für `@jamkremsmuenster` erzeugen.
2. Den erhaltenen Embed-Code (HTML/JS-Schnipsel) im Admin-Panel unter
   **Configuration → Theme** in das Feld `instagram_embed_html` einfügen.
3. Dieser Code hat automatisch Vorrang vor Variante A, falls beides
   eingerichtet ist.

## 7. Design anpassen (sobald Logo/Bilder vorliegen)

- Farben, Schrift und Layout: `user/themes/jam-theme/css/theme.css`
  (Variablen am Dateianfang unter `:root`).
- Logo: aktuell Platzhalter-Text "j@m" in
  `user/themes/jam-theme/templates/partials/header.html.twig` – kann dort
  gegen ein `<img>`-Tag mit echtem Logo getauscht werden.
- Aktuelle Richtung "Dschungel-Lounge": tiefes Grün (`--color-dark`),
  Logo-Orange (`--color-primary`) und warmes Creme (`--color-bg`) – bewusst
  bildlastig (große Titelbilder bei Neuigkeiten) und kontrastreich gehalten,
  damit es für Jugendliche einladend statt behördlich wirkt.
