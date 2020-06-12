## Writing data source plugins

A variety of reports are bundled with this module, but it is easy for developers to add additional reports. The best way to do this is to write a submodule that provides a data source plugin. Additional functionality (for example, a custom input form) can be added as well. Submodules let you package reports that are specific to your site, or mangage dependencies on third-part Drupal modules.

The most typical source for report data is Drupal's database, but data source plugins can get their data from Solr, Blazegraph, or log files on disk (for example). Sample submodules are available in the 'modules' subdirectory:

* "Flavors", a very simple example module for developers
* "People currently in space", which demonstrates how a report can get data from a remote API
* a report of randomly generated pie chart data
* a report of randomly generated bar chart data
* a sample report that generates HTML markup instead of data for a Chart.js chart

The minimum requirements for a submodule that provides a data source plugin are:

1. a .info.yml file
1. a .services.yml file
   * Within the .services.yml file, the service ID must be in the form `islandora_repository_reports.datasource.xxx`, where `xxx` is specific to the plugin. This pattern ensures that the plugin will show up in the list of media formats reports in the select list in the reports form.
1. a plugin class file that implements the `MediaFormatsReportsDataSourceInterface` interface.
   * The chart visualizing your data can be either a Chart.js [pie](https://www.chartjs.org/samples/latest/charts/pie.html) or [bar](https://www.chartjs.org/samples/latest/charts/bar/vertical.html) chart with a single data series. You do not need to interact with the Chart.js Javascript directly; all you need to do is have your plugin's `getData()` method return an associative array containing `label => count` member arrays.
   * Data source plugins can also generate HTML markup (more accurately, a render array) that is displayed to the user instead of Chart.js charts.
1. Optionally, a .module file containing any standard Drupal hook implementations. The most common hook you will want to implement is `hook_form_form_id_alter()` to add/delete form elements to the report selector form, e.g., `YOUR_MODULE_form_islandora_repository_reports_report_selector_alter()`. See the [comments](modules/islandora_repository_reports_datasource_random/islandora_repository_reports_datasource_random.module#L11) in the random data source plugin's .module file for more information.

