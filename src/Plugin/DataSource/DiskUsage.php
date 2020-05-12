<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets disk usage by Drupal filesystem.
 */
class DiskUsage implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Disk usage');
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
  public function getChartTitle() {
    return '@total GB total disk usage, grouped by Drupal filesystem.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $database = \Drupal::database();
    $result = $database->query("SELECT uri, filesize FROM {file_managed}");

    $filesystem_usage = [];
    foreach ($result as $row) {
      $filesystem = strtok($row->uri, ':');
      if (array_key_exists($filesystem, $filesystem_usage)) {
        $filesystem_usage[$filesystem] = $filesystem_usage[$filesystem] + $row->filesize;
      }
      else {
        $filesystem_usage[$filesystem] = $row->filesize;
      }
    }

    // Drupal gives us bytes, so we convert to GB.
    foreach ($filesystem_usage as $filesystem => $usage) {
      $filesystem_usage[$filesystem] = round($usage / 1024 / 1024 / 1024, 3);
    }

    return $filesystem_usage;
  }
}
