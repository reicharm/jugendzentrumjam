# Konzept: Website für das JugendImpulsZentrum j@m Kremsmünster

## Ausgangslage

- Verein betreibt bereits ein eigenes Webserver-Hosting (klassisches PHP-Hosting, Zugriff per FTP/SFTP).
- WordPress ist bekannt, wird aber als **zu komplex** für den eigentlichen Bedarf empfunden (zu viele Optionen, Update-Pflege, Plugin-Wildwuchs).
- Kernanforderung ist bewusst schlank: **Neuigkeiten** und **Termine** sollen von **mehreren Redakteur:innen** unkompliziert veröffentlicht werden können.
- Bilder, Logo und weiteres Design-Material folgen später – das Grundgerüst muss also unabhängig davon funktionieren.

## Empfehlung: Grav CMS

**[Grav](https://getgrav.org)** ist ein schlankes, dateibasiertes CMS (PHP, kein Datenbankserver nötig):

| Kriterium | Warum Grav passt |
|---|---|
| Einfachheit | Admin-Oberfläche mit übersichtlichem WYSIWYG-Editor, deutlich reduzierter als WordPress – genau zwei Inhaltstypen (News, Termine) statt "alles kann alles". |
| Hosting | Läuft auf normalem PHP-Webhosting per FTP-Upload, **keine Datenbank** nötig (Inhalte sind Markdown-Dateien) → passt zu eurem bestehenden Webserver, kein zusätzliches DB-Setup, einfache Backups (einfach Ordner kopieren). |
| Mehrere Redakteure | Eigenes Benutzer- & Rollensystem im Admin-Panel (Login, Rechte pro Person), jede/r Redakteur:in bekommt einen eigenen Account. |
| Kosten | Komplett kostenlos & Open Source (GPL), keine Lizenzgebühren. |
| Wartungsaufwand | Deutlich kleinere Angriffsfläche als WordPress (kein riesiges Plugin-Ökosystem von Drittanbietern nötig), seltener sicherheitskritische Updates. |
| Pflege der Inhalte | Redakteur:innen sehen nur "Neuigkeit erstellen" / "Termin erstellen" mit klaren Feldern (Titel, Text, Bild, Datum, Ort) statt eines allgemeinen Seiten-Baukastens. |

### Alternativen (kurz geprüft, nicht empfohlen)

- **Kirby CMS** – ähnlich schlank und sehr angenehme Bedienung, aber **kostenpflichtige Lizenz** (einmalig, pro Site) nötig für den produktiven Einsatz.
- **Baukästen (Jimdo/Wix/Webador)** – am einfachsten für absolute Laien, aber laufende Abo-Kosten und ihr würdet das bereits vorhandene eigene Hosting nicht nutzen.
- **WordPress** – bewusst verworfen, da laut Rückmeldung bereits bekannt und als zu komplex empfunden.

## Struktur der Website

1. **Startseite** – Kurzvorstellung j@m + die 3–4 neuesten Neuigkeiten + die nächsten anstehenden Termine auf einen Blick.
2. **Neuigkeiten** – chronologische Liste, jede Neuigkeit mit Titel, Text, optionalem Bild, Veröffentlichungsdatum.
3. **Termine** – Liste kommender Termine (Titel, Datum, Uhrzeit, Ort, Beschreibung), vergangene Termine fallen automatisch aus der Übersicht.
4. **Über uns / Kontakt** – feste Seite (Adresse, Öffnungszeiten, Ansprechpersonen, Anfahrt) – wird einmalig gepflegt, nicht laufend.

Inhaltlich bewusst reduziert auf das, was laufend gepflegt wird (News/Termine), plus 1–2 statische Seiten.

## Redaktionsprozess für mehrere Personen

- Jede Redaktionsperson erhält einen eigenen Login im Grav-Admin-Panel (`/admin`) mit der Rolle **"Redakteur"**.
- Neuigkeit veröffentlichen: **Neuigkeiten → Neue Seite → Formular ausfüllen → Veröffentlichen.** Kein technisches Wissen nötig.
- Termin veröffentlichen: gleiches Prinzip, eigenes Formular mit Datum/Uhrzeit/Ort-Feldern (siehe `grav/user/themes/jam-theme/blueprints/`).
- Änderungen sind sofort live, keine Freigabeprozesse nötig (kann bei Bedarf später ergänzt werden).

## Technisches Grundgerüst in diesem Repository

Da Grav selbst (Core-System) ein eigenständiges, separat herunterzuladenes PHP-Framework ist, wird es **nicht** ins Repo eingecheckt. Stattdessen enthält dieses Repo das **Theme + die Formular-Definitionen (Blueprints)**, die auf ein frisches Grav-Setup aufgesetzt werden:

```
grav/
  user/
    themes/jam-theme/       # Theme (Twig-Templates, CSS)
    blueprints/pages/       # Formularfelder für Neuigkeit & Termin im Admin
```

Siehe `grav/README.md` für die konkrete Installationsanleitung auf eurem Webhosting.

## Update: Zielgruppe Jugendliche + Instagram

Nach Rückmeldung wurde das Grundgerüst angepasst:

- **Bildlastige Neuigkeiten:** Jeder Beitrag bekommt ein großes Titelbild, das auf Startseite, Übersicht und im Beitrag prominent erscheint (statt reiner Textliste) – Neuigkeiten funktionieren damit wie ein Blog.
- **Über uns** stellt jetzt Mitarbeitende (Foto, Name, Rolle) und Räume (Foto, Name, Beschreibung) als Kacheln vor, beliebig erweiterbar über das Admin-Panel.
- **Instagram:** Footer und Startseite verlinken auf `@jamkremsmuenster`; optional lässt sich ein echter Live-Feed per kostenlosem Widget-Dienst (z. B. Elfsight/SnapWidget) einbinden, ohne Code anzufassen (siehe `grav/README.md`, Abschnitt 6). Ein direkter automatischer Abruf des Instagram-Designs war nicht möglich, da Instagram externe Zugriffe blockiert.
- **Design "Dschungel-Lounge":** tiefes Grün, Logo-Orange und warmes Creme, bewusst kontrastreicher und bildlastiger als eine reine "Vereinsseite" – siehe Design-Vorschläge-Artefakt für die Herleitung aus Logo und Fotos des Zentrums.

## Offene Punkte (folgen später laut Rückmeldung)

- Logo, Bildmaterial, Farbwelt → aktuell Platzhalter-Design (neutral, mobil-optimiert), leicht austauschbar über `assets/css/theme.css`.
- Endgültige Texte für "Über uns" / Kontakt.
- Domain-Anbindung auf dem bestehenden Hosting.
