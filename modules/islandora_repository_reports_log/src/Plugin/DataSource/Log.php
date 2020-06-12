<?php

namespace Drupal\islandora_repository_reports_log\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets disk usage by Drupal filesystem.
 */
class Log implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('System log');
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
    return t('@total entries in the Drupal system log.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $facet = $utilities->getFormElementDefault('islandora_repository_reports_log_facet', 'type');

    $database = \Drupal::database();
    $log_entries = [];

    if ($facet == 'type') {
      $result = $database->query("SELECT type, count(*) AS count FROM {watchdog} GROUP BY type ORDER BY count");
      foreach ($result as $row) {
        $log_entries[ucfirst($row->type)] = $row->count;
      }
    }

    if ($facet == 'severity') {
      $result = $database->query("SELECT severity, count(*) AS count FROM {watchdog} GROUP BY severity ORDER BY count");
      $levels = [
        '0' => 'Emergency',
        '1' => 'Alert',
        '2' => 'Critical',
        '3' => 'Error',
        '4' => 'Warning',
        '5' => 'Notice',
        '6' => 'Info',
        '7' => 'Debug',
      ];

      foreach ($result as $row) {
        $level = $row->{$facet};
        $log_entries[$levels[$level]] = $row->count;
      }
    }

    $this->csvData = [[ucfirst($facet), t('Number of entries')]];
    foreach ($log_entries as $key => $count) {
      $this->csvData[] = [$key, $count];
    }

    return $log_entries;
  }

}
