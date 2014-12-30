# KNVB Data API

Met behulp van deze basis kun je in WordPress gebruik maken van de shortcodes:

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

Toon alle wedstrijden van week `42`

```
[knvb uri="/wedstrijden" extra="weeknummer=42"]
```

Toon de stand van het team met ID `106698`

```
[knvb uri="/teams/106698/ranking"]
```
