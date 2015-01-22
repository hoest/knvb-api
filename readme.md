# KNVB Data API

Met behulp van deze basis kun je in WordPress gebruik maken van de shortcodes:

Toon resultaten, standen en programma's van diverse team-ID's

```
[knvbteam list="106698;106699"]
```

Toon alle teams met bijbehorende ID's

```
[knvb uri="/teams"]
```

Toon de uitslagen van het team met ID `106698`

```
[knvb uri="/teams/106698/results"]
```

Toon het programma van het team met ID `106698`

```
[knvb uri="/teams/106698/schedule"]
```

Toon alle wedstrijden

```
[knvb uri="/wedstrijden" extra="weeknummer=A"]
```

Toon alle wedstrijden, thuis- en uitwedstrijden gesplitst

```
[knvb uri="/wedstrijden" extra="weeknummer=A&thuis=1"]
```

Toon alle wedstrijden van vorige week

```
[knvb uri="/wedstrijden" extra="weeknummer=P"]
```

Toon alle wedstrijden van deze week

```
[knvb uri="/wedstrijden" extra="weeknummer=C"]
```

Toon alle wedstrijden van volgende week

```
[knvb uri="/wedstrijden" extra="weeknummer=N"]
```

Toon alle wedstrijden van week `42`

```
[knvb uri="/wedstrijden" extra="weeknummer=42"]
```

Toon de stand van het team met ID `106698`

```
[knvb uri="/teams/106698/ranking"]
```

Om een simpele slider te tonen van een team met als eerste de eerstvolgende
wedstrijd en vervolgens alle vorige wedstrijden met uitslagen, dan kun je
dit bereiken door het volgende op te nemen in je template code:

Je moet wel al [jQuery](http://www.jquery.com/) gebruiken in je template, anders
zul je deze nog even moet opnemen in je template.

Voeg daarnaast deze CSS toe aan je template:

```html
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/knvb-api/slider.css" />
```

Dit JavaScript bestand:

```html
<script src="/wp-content/plugins/knvb-api/slider.js"></script>
```

Vervolgens kun je met deze shortcode de slider plaatsen:

```
[knvbteam-slider id="106698"]
```

Wanneer je de logo's van de teams wilt tonen in een bepaald overzicht, dan
vul je in het `extra` attribuut `logo=1` toe. Bijvoorbeeld:

```
[knvb uri="/wedstrijden" extra="weeknummer=A&logo=1"]
```
