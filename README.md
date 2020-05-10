# Islandora Repository Reports

## Introduction

A Drupal 8 module that provides a lightweight framework for reporting on various aspects of and Islandora repository using charts. Default reports are:

* Media by MIME type
* Nodes by Islandora model
* Nodes by genre

Submodules are included that add a report of media by [PRONOM PUID](https://en.wikipedia.org/wiki/PRONOM) (if Islandora FITS is installed), and two sample reports, "Flavors" and one that generated random report data.

## Overview

Users with "Administer Site Configuration" can visit the reports page from Drupal's Reports list. The link to "Islandora Repository" will show the default MIME type report:

![MIME type report](docs/images/islandora_repo_reports.png)

Checking the "Generate a CSV file of this data" box and clicking the "Go" button will provide a link to download the CSV file.

## Configuration

There is only one global configuration option, whether or not to cache report data. If you select this option, you should periodically pregenerate report data as described below.

Users will need to have the "View Repository Reports" permission to view the report page.
	
## Pregenerating report data

This module comes with a set of Drush commands that generates the data used in the reports and caches it:

1. To list the enabled services that generate report data: `drush islandora_repository_reports:list_report_types`
1. To pregenerate the data for the 'puid' report: `drush islandora_repository_reports:build_cache puid`
1. To delete the data for the 'mimetype' report: `islandora_repository_reports:delete_cache mimetype`

## Requirements

* [Islandora 8](https://github.com/Islandora/islandora)
* [Islandora FITS](https://github.com/roblib/islandora_fits) is required if you want to generate the PUID report.

## Installation

1. Clone this repo into your Islandora's `drupal/web/modules/contrib` directory.
1. Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports`.

## Writing custom data source plugins

Data for the MIME type, Islandora Model, and PUID report are taken from Drupal's database, but data source plugins can get their data from Solr, Blazegraph, or even a source external to Islandora.

The `modules` subdirectory contains two sample data source plugin. The minimum requirements for a data source plugin are:

1. a .info.yml file
1. a .services.yml file
   * Within the .services.yml file, the service ID must be in the form `islandora_repository_reports.datasource.xxx`, where `xxx` is specific to the plugin. This pattern ensures that the plugin will show up in the list of media formats reports in the select list in the reports form.
1. a plugin class file that implements the `MediaFormatsReportsDataSourceInterface` interface.
   * The plugin's `getData()` method needs to return an associative array containing formatname => count members.
1. Optionally, a .module file containing any standard Drupal hook implementations. For example, data source plugins can add form elements to the report selector form. See the comments in the random data source plugin's .module file for more information.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
