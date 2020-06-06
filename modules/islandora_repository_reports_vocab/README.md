# Vocabulary Plugin for Islandora Repository Reports

Data source plugin for the Islandora Repository Reports module that allows the user to get a report on the usage of any vocabulary.

This module has a couple of limitations:

1. It can only generate a report on the frequency of vocabulary term usage by nodes (not media, users, etc.).
1. It can only generate a report on on content type at a time.

## Requirements

* [Islandora Repository Reports](https://github.com/mjordan/islandora_repository_reports)

## Installation

Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports_vocab`.

## Configuration

None.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
