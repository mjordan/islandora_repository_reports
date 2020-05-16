<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes created by month.
 */
class NodesByMonth implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Nodes by month created');
  }

  /**
   * {@inheritdoc}
   */
  public function getChartType() {
    return 'bar';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle() {
    return '@total nodes broken down by month created.';
  }

  /**
   * {@inheritdoc}
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
