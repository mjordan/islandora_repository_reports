<?php

namespace Drupal\islandora_repository_reports_bagger\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets disk usage by Drupal filesystem.
 */
class Bags implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Variable $csvData is an array of arrays corresponding to CSV records.
   *
   * @var string
   */
  public $csvData;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Bags registered with Islandora Bagger Integration');
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
    return 'pie';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle($total) {
    return t('@total Bags registered with Islandora Bagger Integration.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $bag_report_type = $utilities->getFormElementDefault('islandora_repository_reports_bagger_report_type', 'hash_algorithm');

    $database = \Drupal::database();
    $result = $database->query("SELECT $bag_report_type, COUNT($bag_report_type) AS count FROM {islandora_bagger_integration_bag_log} GROUP BY $bag_report_type");

    $counts = [];
    foreach ($result as $bag) {
      $counts[$bag->{$bag_report_type}] = $bag->count;
    }

    $this->csvData = [[$bag_report_type, 'Occurances']];
    foreach ($counts as $type => $count) {
      $this->csvData[] = [$type, $count . ' Bags'];
    }

    return $counts;
  }

}
