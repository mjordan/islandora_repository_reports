<?php

namespace Drupal\islandora_repository_reports_datasource_random_bar\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Random data source for the Islandora Repository Reports module.
 */
class Random implements IslandoraRepositoryReportsDataSourceInterface{

  /**
   * Returns the data source's name.
   *
   * @return string
   *   The name of the data source.
   */
  public function getName() {
    return t('Random data for bar charts (for testing, etc.)');
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
   * Generates the random data.
   *
   * @return array
   *   An assocative array containing formatlabel => count members. 
   */
  public function getData() {
    if ($tempstore = \Drupal::service('user.private_tempstore')->get('islandora_repository_reports')) {
      if ($form_state = $tempstore->get('islandora_repository_reports_report_form_values')) {
        $num_data_elements = $form_state->getValue('islandora_repository_reports_datasource_random_bar_num_data');
      }
    }
    else {
      $num_data_elements = 5;
    }
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $data = [];
    for ($x = 1; $x <= $num_data_elements; $x++) {
      $label = ucfirst(substr(str_shuffle($chars), 3, 12));
      $data[$label] = rand(0,1000);
    } 
    return $data;
  }

}
