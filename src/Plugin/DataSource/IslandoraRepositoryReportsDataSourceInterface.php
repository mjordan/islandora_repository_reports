<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Gets data for rendering in an Islandora Repository Reports report.
 */
interface IslandoraRepositoryReportsDataSourceInterface {

  /*
   * Data source plugins should define a public variable $csvData, which
   * is an array of arrays corresponding to CSV records. All data source
   * plugins should populate this variable in their getData() method. In
   * addition, HTML report plugins should call $utilities->writeCsvFile()
   * and pass in their $csvData variable there as well (this method is
   * called automatically for Chart.js reports). If a plugin does not
   * generate data that can be downloaded as a CSV, use hook_form_alter()
   * to set the "Generate a CSV of this data" form widget to 'invisible'.
   * See the Matomo data source module for an example.
   */

  /**
   * Returns the data source's name.
   *
   * @return string
   *   The name of the data source as it appears in the reports form.
   */
  public function getName();

  /**
   * Indicates whether the report is on nodes, media, etc.
   *
   * Currently used only to indicate whether or not to show the content type
   * list on the report selection form.
   *
   * @return null|string
   *   The Drupal entity type. Return null if the report is not a Drupal entity.
   */
  public function getBaseEntity();

  /**
   * Returns the data source's chart type.
   *
   * @return string
   *   Either 'pie' or 'bar'.
   */
  public function getChartType();

  /**
   * Returns the data source's chart title.
   *
   * @param string $total
   *   The total number of data points in the chart. Is interpolated
   *   into Drupal's t() function.
   *
   * @return string
   *   The chart's title.
   */
  public function getChartTitle($total);

  /**
   * Gets the data.
   *
   * @return array
   *   An assocative array containing dataLabel => count members.
   */
  public function getData();

}
