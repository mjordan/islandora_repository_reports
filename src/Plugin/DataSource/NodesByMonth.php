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
    if ($tempstore = \Drupal::service('user.private_tempstore')->get('islandora_repository_reports')) {
      if ($form_state = $tempstore->get('islandora_repository_reports_report_form_values')) {
        $start_of_range = $form_state->getValue('islandora_repository_reports_nodes_by_month_range');
      }
    }
    else {
      $start_of_range = NULL;
    }
    $start_of_range = trim($start_of_range);
	  
    $database = \Drupal::database();
    $result = $database->query("SELECT created FROM {node_field_data}");

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

    return $created_counts;
  }
}
