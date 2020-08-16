# OAI-PMH Harvests Data Source Plugin for Islandora Repository Reports

Data source plugin for the Islandora Repository Reports module that shows number of OAI-PMH harvests, by month.

## Requirements

* [Islandora Repository Reports](https://github.com/mjordan/islandora_repository_reports)
* [REST OAI-PMH](https://www.drupal.org/project/rest_oai_pmh/)

## Overview

This module records usage of the endpoint provided by the REST OAI-PMH module, starting immediately after this module is enabled. It then renders a summary of that usage as a bar chart, broken down by month.

OAI-PMH harvesters make a number of requests to a repository to get all of the records. The first request is different from subsequent requests that are part of the same harvest in that they do not contain a `resumptionToken` argument. Using this pattern, this module defines a "harvest" by the initial request to the OAI-PMH endpoint contains the `ListRecords` verb but does not contain the request argument `resumptionToken`.

## Installation

Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports_oai_usage`.

## Configuration

There are no configuration settings for this module.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
