# KNVB Data API

## LET OP:
Deze plugin is niet meer up-to-date en zal niet meer vernieuwen. De KNVB heeft mijn plugin overgenomen en gezamenlijk met een leverancier een plugin op de markt gebracht voor WordPress: https://nl.wordpress.org/plugins/shortcodes-knvb-api/

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

## Slider
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

## Logo's
Wanneer je de logo's van de teams wilt tonen in een bepaald overzicht, dan
vul je in het `extra` attribuut `logo=1` toe. Bijvoorbeeld:

```
[knvb uri="/wedstrijden" extra="weeknummer=A&logo=1"]
```

## Eigen templates
De `[knvb ...]` shortcode is uit te breiden met een `template="..."`
attribuut. Voorbeeld:

```
[knvb uri="/wedstrijden" template="custom"]
```

Nu zal er gezocht worden naar het template
`./wp-content/plugins/knvb-api/templates/custom_template.html`. Dit
template kun je vervolgens zelf opbouwen en plaatsen. Voorbeelden
voor een template kun je vinden in de map
`./wp-content/plugins/knvb-api/templates/`. Deze templates kun je niet
zo maar aanpassen, aangezien ze standaard meegeleverd worden met de
release:

* \templates\ranking.html
* \templates\results.html
* \templates\results_slider.html
* \templates\schedule.html
* \templates\schedule_slider.html
* \templates\teams.html
* \templates\wedstrijden.html

## Release

1. Draai `svn update` in de KNVB-API SVN repository
2. Pas in de GIT repo het release-nummer aan in `readme.txt` en `knvb-api-plugin.php`
3. Draai `git commit`
4. Kopieer de wijzigingen vanuit de GIT repo naar de SVN repo in de `trunk` map
5. Controleer je wijzigingen in de SVN repo met `svn stat` en `svn diff`
6. Leg de wijzigingen vast met `svn ci -m "..."`
7. Maak een tag door eerste kopieren: `svn cp trunk tags/2.0`
8. En daarna te committen: `svn ci -m "tagging version 2.0"`

## Bijdragen geleverd door

* [hoest](https://github.com/hoest/)
* [thepercival](https://github.com/thepercival)
