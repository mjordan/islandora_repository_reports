# OAI-PMH Harvests Data Source Plugin for Islandora Repository Reports

Data source plugin for the Islandora Repository Reports module that shows number of OAI-PMH harvests, by month.

## Requirements

* [Islandora Repository Reports](https://github.com/mjordan/islandora_repository_reports)
* [REST OAI-PMH](https://www.drupal.org/project/rest_oai_pmh/)

## Overview

This module records usage of the OAI-PMH endpoint provided by the REST OAI-PMH module, starting immediately after this module is enabled. It then renders a summary of that usage as a bar chart, broken down by month.

A "harvest" is a request using the OAI-PMH `ListRecords` verb, e.g., `/oai/request?verb=ListRecords`.

## Installation

Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports_oai_usage`.

## Configuration

There are no configuration settings for this module.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
