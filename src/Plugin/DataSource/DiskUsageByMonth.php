<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets disk usage by Drupal filesystem.
 */
class DiskUsageByMonth implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Disk usage by month');
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
    return t('@total GB total disk usage, broken down by month added.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $start_of_range = $utilities->getFormElementDefault('islandora_repository_reports_disk_usage_by_month_range_start', '');
    $start_of_range = strlen($start_of_range) ? $start_of_range : $utilities->defaultStartDate;
    $start_of_range = trim($start_of_range);
    $end_of_range = $utilities->getFormElementDefault('islandora_repository_reports_disk_usage_by_month_range_end', '');
    $end_of_range = strlen($end_of_range) ? $end_of_range : $utilities->defaultEndDate;
    $end_of_range = trim($end_of_range);

    $database = \Drupal::database();
    $result = $database->query("SELECT filesize, changed FROM {file_managed}");

    $month_usage = [];
    foreach ($result as $row) {
      $label = date("Y-m", $row->changed);
      if ($label >= $start_of_range && $label <= $end_of_range) {
        if (array_key_exists($label, $month_usage)) {
          $month_usage[$label] = $month_usage[$label] + $row->filesize;
        }
        else {
          $month_usage[$label] = $row->filesize;
        }
      }
    }

    // Drupal gives us bytes, so we convert to GB.
    $converted_filesystem_usage = [];
    foreach ($month_usage as $key => $usage) {
      $converted_month_usage[$key] = round($usage / 1024 / 1024 / 1024, 4);
    }

    $this->csvData = [[t('Month'), 'Usage (GB)']];
    foreach ($converted_month_usage as $type => $usage) {
      $this->csvData[] = [$type, $usage];
    }

    return $converted_month_usage;
  }

}
