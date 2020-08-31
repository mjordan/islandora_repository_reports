# OAI-PMH Harvests Data Source Plugin for Islandora Repository Reports

Data source plugin for the Islandora Repository Reports module that shows number of OAI-PMH harvests, by month.

## Requirements

* [Islandora Repository Reports](https://github.com/mjordan/islandora_repository_reports)
* [REST OAI-PMH](https://www.drupal.org/project/rest_oai_pmh/)

## Overview

This module records usage of the endpoint provided by the REST OAI-PMH module, starting immediately after this module is enabled. It then renders a summary of that usage as a bar chart, broken down by month.

To complete an OAI-PMH harvest, a harvester must make one or, usually, more than one request to a repository's OAI-PMH endpoint. The initial request is different from subsequent requests that are part of the same harvest in that it does not contain a `resumptionToken` argument. Taking advantage of this pattern, this module defines a "harvest" by the request to the OAI-PMH endpoint that contains the `ListRecords` verb but does not contain the request argument `resumptionToken`.

## Installation

Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports_oai_usage`.

## Configuration

There are no configuration settings for this module.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
