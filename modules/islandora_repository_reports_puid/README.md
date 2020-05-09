# PRONOM PUID Data Source

Data source plugin for the Islandora Repository Reports module that counts media by PRONOM PUID.

> Note: https://github.com/roblib/islandora_fits/commit/004f269ebfe50462eff9a592a9e76c5fe9f115bf removed indexing of FITS fields, breaking this data source plugin. Once we revert that ability, this data source plugin will work. In the meantime, please use the MIME Type report.

## Requirements

* [Islandora Repository Reports](https://github.com/mjordan/islandora_repository_reports)
* [Islandora FITS](https://github.com/roblib/islandora_fits)

## Installation

Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports_puid`.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
