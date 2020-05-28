<?php

namespace Drupal\islandora_repository_reports\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush commandfile.
 */
class IslandoraRepositoryReportsCommands extends DrushCommands {

  /**
   * Lists available report types.
   *
   * @command islandora_repository_reports:list_report_types
   * @usage islandora_repository_reports:list_report_types
   */
  public function listReportTypes() {
    $output = $this->output();
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $services = $utilities->getServices(TRUE);
    foreach ($services as $service_id) {
      $output->writeln($service_id);
    }
  }

  /**
   * Generates data for specified report type and caches it.
   *
   * @param string $report_type
   *   The type of report (e.g., 'mimetype', 'model', 'puid').
   *
   * @command islandora_repository_reports:build_cache
   * @usage islandora_repository_reports:build_cache mimetype
   */
  public function buildCache($report_type) {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $services = $utilities->getServices(TRUE);

    if (!in_array($report_type, $services)) {
      $this->logger()->error(dt('Report type @report_type not found.', ['@report_type' => $report_type]));
      exit();
    }

    $data_source_service_id = 'islandora_repository_reports.datasource.' . $report_type;
    $data_source = \Drupal::service($data_source_service_id);
    $counts = $data_source->getData();
    $cid = 'islandora_repository_reports_data_' . $report_type;
    \Drupal::cache()->set($cid, $counts);
    $this->logger()->notice(dt('Cache built for Islandora Repository Report @report_type.', ['@report_type' => $report_type]));
  }

  /**
   * Deletes the cached data for specified report type.
   *
   * @param string $report_type
   *   The type of report (e.g., 'mimetype', 'model', 'puid').
   *
   * @command islandora_repository_reports:delete_cache
   * @usage islandora_repository_reports:delete_cache mimetype
   */
  public function deleteCache($report_type) {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $services = $utilities->getServices(TRUE);

    if (!in_array($report_type, $services)) {
      $this->logger()->error(dt('Report type @report_type not found.', ['@report_type' => $report_type]));
      exit();
    }

    $cid = 'islandora_repository_reports_data_' . $report_type;
    \Drupal::cache()->delete($cid);
    $this->logger()->notice(dt('Cache deleted for Islandora Repository Report @report_type.', ['@report_type' => $report_type]));
  }

}
