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
    return '@total GB total disk usage.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    if ($tempstore = \Drupal::service('user.private_tempstore')->get('islandora_repository_reports')) {
      if ($form_state = $tempstore->get('islandora_repository_reports_report_form_values')) {
        $disk_usage_type = $form_state->getValue('islandora_repository_reports_disk_usage_type');
      }
    }
    else {
      $disk_usage_type = 'filesystem';
    }
	  
    $database = \Drupal::database();
    $result = $database->query("SELECT uri, filesize, filemime FROM {file_managed}");

    $filesystem_usage = [];
    if ($disk_usage_type == 'filesystem') {
      foreach ($result as $row) {
        $filesystem = strtok($row->uri, ':');
        if (array_key_exists($filesystem, $filesystem_usage)) {
          $filesystem_usage[$filesystem] = $filesystem_usage[$filesystem] + $row->filesize;
        }
        else {
          $filesystem_usage[$filesystem] = $row->filesize;
	}
      }
    }

    if ($disk_usage_type == 'mimetype') {
      foreach ($result as $row) {
        if (array_key_exists($row->filemime, $filesystem_usage)) {
          $filesystem_usage[$row->filemime] = $filesystem_usage[$row->filemime] + $row->filesize;
        }
        else {
          $filesystem_usage[$row->filemime] = $row->filesize;
        }
      }
    }

    // Drupal gives us bytes, so we convert to GB.
    $converted_filesystem_usage = [];
    foreach ($filesystem_usage as $key => $usage) {
      $converted_filesystem_usage[$key] = round($usage / 1024 / 1024 / 1024, 4);
    }

    return $converted_filesystem_usage;
  }
}
