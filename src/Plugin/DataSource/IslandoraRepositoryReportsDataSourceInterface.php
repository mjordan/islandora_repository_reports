<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Gets data for rendering in an Islandora Repository Reports report.
 */
interface IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Returns the data source's name.
   *
   * @return string
   *   The name of the data source as it appears in the reports form.
   */
  public function getName();

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
   * This is run through Drupal's t() by the caller. Include the placeholder
   * '@total' to interpolate the cumulative total in the title.
   *
   * @return string
   */
  public function getChartTitle();

  /**
   * Gets the data.
   *
   * @return array
   *   An assocative array containing dataLabel => count members.
   */
  public function getData();

}
