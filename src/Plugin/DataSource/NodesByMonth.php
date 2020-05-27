<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets nodes created by month.
 */
class NodesByMonth implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * An array of arrays corresponding to CSV records.
   *
   * @var string
   */
  public $csvData;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Nodes by month created');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return 'node';
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
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $start_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range', '');
    $start_of_range = trim($start_of_range);

    $database = \Drupal::database();
    $node_types = $utilities->getSelectedContentTypes();
    $result = $database->query("SELECT created FROM {node_field_data} WHERE type in (:types[])",
      [':types[]' => $utilities->getSelectedContentTypes()]
    );

    $created_counts = [];
    foreach ($result as $row) {
      $label = date("Y-m", $row->created);
      // This is lazy; the SQL query should include the start date.
      if ($label >= $start_of_range) {
        if (array_key_exists($label, $created_counts)) {
          $created_counts[$label]++;
        }
        else {
          $created_counts[$label] = 1;
        }
      }
    }

    $this->csvData = [[t('Month created'), 'Count']];
    foreach ($created_counts as $month => $count) {
      $this->csvData[] = [$month, $count];
    }

    return $created_counts;
  }

}
