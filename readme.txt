=== KNVB Api plugin ===
Contributors: hoest
Donate link: http://www.hoest.nl/
Tags: knvb, voetbal, api, soccer, dutch
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 1.3

This plugin can be used for Dutch football clubs with a WordPress wedsite and a API-key for the KNVB data-API: http://www.knvbdataservice.nl/

== Description ==

Show teams:

`[knvb uri="/teams"]`

Show results for the team with ID `106698`

`[knvb uri="/teams/106698/results"]`

Show schedule for the team with ID `106698`

`[knvb uri="/teams/106698/schedule"]`

Show all games

`[knvb uri="/wedstrijden" extra="weeknummer=A"]`

More info at [GitHub](https://github.com/hoest/knvb-api/blob/master/readme.md)

== Installation ==

1. Upload the plugin-folder to the `/wp-content/plugins/` directory
2. The plugin need to create a `./cache/` folder with files: `chmod 777`
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

None

== Screenshots ==

None

== Changelog ==

Check [the closed issues](https://github.com/hoest/knvb-api/issues?q=is%3Aissue+is%3Aclosed) on GitHub.

== Upgrade Notice ==

None

== Arbitrary section ==

None
