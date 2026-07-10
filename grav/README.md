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

Der Footer und die Startseite verlinken automatisch auf
`instagram.com/jamkremsmuenster` (Handle in `jam-theme.yaml` unter
`instagram_handle` änderbar). Für einen eingebetteten Live-Feed (nicht nur
einen Link) empfiehlt sich ein kostenloser Feed-Widget-Dienst, da Instagram
selbst kein einfaches Embed mehr ohne Entwickler-API anbietet:

1. Bei einem Anbieter wie [elfsight.com](https://elfsight.com) oder
   [snapwidget.com](https://snapwidget.com) einen kostenlosen "Instagram
   Feed"-Widget-Code für `@jamkremsmuenster` erzeugen.
2. Den erhaltenen Embed-Code (HTML/JS-Schnipsel) im Admin-Panel unter
   **Configuration → Theme** in das Feld `instagram_embed_html` einfügen.
3. Der Feed erscheint danach automatisch unter dem "Folgt uns"-Button auf
   der Startseite.

Ohne diesen Schritt funktioniert die Seite trotzdem – es wird dann nur der
Folgen-Button angezeigt.

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
