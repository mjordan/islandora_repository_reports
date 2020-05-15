<?php

namespace Drupal\islandora_repository_reports_randtab\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Random data source for the Islandora Repository Reports module.
 */
class RandomTable implements IslandoraRepositoryReportsDataSourceInterface{

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Random data for tabular data (for testing, etc.)');
  }

  /**
   * {@inheritdoc}
   */
  public function getChartType() {
    return 'html';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle() {
    return '@total total random data points.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $data = [];
    for ($x = 1; $x <= $num_data_elements; $x++) {
      $label = ucfirst(substr(str_shuffle($chars), 3, 12));
      $data[$label] = rand(0,1000);
    } 

    $header = ['Column 1', 'Column 2'];
    $rows = [
      ['Row 1 column 1', 'Row 1 column 2'],
      ['Row 2 column 1', 'Row 2 column 2'],
    ];

    return [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
  }

}
