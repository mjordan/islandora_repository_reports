<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Gets data for rendering in a media formats report.
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
   * Gets the data.
   *
   * @return array
   *   An assocative array containing formatlabel => count members. 
   */
  public function getData();

}
