# Islandora Bagger Data Source Plugin for Islandora Repository Reports

Data source plugins for the Islandora Repository Reports module that:

* show number of items in the Islandora Bagger queue
* provide a report on the number of Bags registered with Islandora Bagger Integration, broken down by creating user, IP address of Islandora Bagger, hash algorithm, and Bagit version. In order for a Bag to be registered, the configuration file used to create it must contain `register_bags_with_islandora: true` (see Islandora Bagger's [README](https://github.com/mjordan/islandora_bagger) for more information.

## Requirements

* [Islandora Repository Reports](https://github.com/mjordan/islandora_repository_reports)
* [Islandora Bagger Integration](https://github.com/mjordan/islandora_bagger_integration)

## Overview

This module renders the Islandora Bagger queue in a list. The other report, which provides a breakdown by creating user, IP address, etc., is a pie chart.

## Installation

Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports_bagger`.

## Configuration

Visit `/admin/config/islandora/islandora_repository_reports`.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
