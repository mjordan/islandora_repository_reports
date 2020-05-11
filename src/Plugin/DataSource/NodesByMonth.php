<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes created by month.
 */
class NodesByMonth implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Returns the data source's name.
   *
   * @return string
   *   The name of the data source.
   */
  public function getName() {
    return t('Nodes created by month');
  }

  /**
   * Returns the data source's chart type.
   *
   * @return string
   *   Either 'pie' or 'bar'.
   */
  public function getChartType() {
    return 'bar';
  }

  /**
   * Gets the data.
   *
   * @return array
   *   An assocative array containing formatlabel => count members. 
   */
  public function getData() {
    $database = \Drupal::database();
    $result = $database->query("SELECT created FROM {node_field_data}");

    $created_counts = [];
    foreach ($result as $row) {
      $label = date("Y-m", $row->created);
      if (array_key_exists($label, $created_counts)) {
        $created_counts[$label]++;
      }
      else {
        $created_counts[$label] = 1;
      }
    }

    return $created_counts;
  }
}
