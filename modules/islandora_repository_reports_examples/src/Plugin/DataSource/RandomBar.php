<?php

namespace Drupal\islandora_repository_reports_examples\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Random data source for the Islandora Repository Reports module.
 */
class RandomBar implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Example: Random data for bar charts');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return NULL;
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
  public function getChartTitle($total) {
    return t('@total total random data points.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $num_data_elements = $utilities->getFormElementDefault('islandora_repository_reports_examples_datasource_random_bar_num_data', 5);
    devel_debug($num_data_elements);

    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $data = [];
    for ($x = 1; $x <= $num_data_elements; $x++) {
      $label = ucfirst(substr(str_shuffle($chars), 3, 12));
      $data[$label] = rand(0, 100);
    }

    $this->csvData = [[t('Random data point'), 'Count']];
    foreach ($data as $label => $count) {
      $this->csvData[] = [$label, $count];
    }

    return $data;
  }

}
