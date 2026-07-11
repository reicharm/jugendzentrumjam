# JugendImpulsZentrum j@m Kremsmünster – Website

Konzept und Grundgerüst für die neue Website des j@m.

- **Konzept & Empfehlung:** siehe [`CONCEPT.md`](CONCEPT.md)
- **Installation auf dem bestehenden Webhosting:** siehe [`grav/README.md`](grav/README.md)

Das Grundgerüst basiert auf [Grav CMS](https://getgrav.org) (kostenlos,
dateibasiert, kein Datenbankserver nötig) mit einem eigenen schlanken Theme
`jam-theme` und zwei Inhaltstypen: **Neuigkeiten** und **Termine**, gedacht
für die Pflege durch mehrere Redakteur:innen über ein einfaches
Admin-Panel.

Logo, Bilder und finale Texte fehlen noch bewusst – das Grundgerüst nutzt
Platzhalter und ist so aufgebaut, dass diese jederzeit einfach ergänzt
werden können (siehe Abschnitt 6 in `grav/README.md`).

## Vorschau auf GitHub Pages

Unter **[reicharm.github.io/jugendzentrumjam](https://reicharm.github.io/jugendzentrumjam/)**
liegt eine statische Vorschau mit den Beispiel-Inhalten (Logo/Design =
"Dschungel-Lounge"-Richtung). Wichtig: Das ist **nur eine Bilder-Vorschau**,
kein lauffähiges CMS – Admin-Panel, echtes Bearbeiten von Neuigkeiten/Terminen
usw. funktionieren erst nach der Installation auf eurem eigenen Webhosting
(siehe `grav/README.md`).

Die Vorschau in `docs/` wird über `scripts/build-static-preview.sh` erzeugt
(installiert kurzzeitig einen echten Grav-Core, rendert alle Seiten und
speichert sie als reines HTML) und muss nach größeren Theme-Änderungen manuell
neu erzeugt und committet werden.
