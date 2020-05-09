<?php

namespace Drupal\islandora_repository_reports\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush commandfile.
 */
class IslandoraRepositoryReportsCommands extends DrushCommands {

  /**
   * @command islandora_repository_reports:list_report_types
   * @usage islandora_repository_reports:list_report_types
   */
   public function list_report_types() {
     $output = $this->output();
     $utilities = \Drupal::service('islandora_repository_reports.utilities');
     $services = $utilities->getServices(TRUE);
     foreach ($services as $service_id) {
       $output->writeln($service_id);
     }
   }

  /**
   * @param string $report_type
   *   The type of report (e.g., 'mimetype', 'model', 'puid').
   *
   * @command islandora_repository_reports:build_cache
   * @usage islandora_repository_reports:build_cache mimetype
   */
   public function build_cache($report_type) {
     $utilities = \Drupal::service('islandora_repository_reports.utilities');
     $services = $utilities->getServices(TRUE);

     if (!in_array($report_type, $services)) {
       $this->logger()->error(dt('Report type @report_type not found.', ['@report_type' => $report_type]));
       exit();
     }

     $data_source_service_id = 'islandora_repository_reports.datasource.' . $report_type;
     $data_source = \Drupal::service($data_source_service_id);
     $format_counts = $data_source->getData();
     $cid = 'islandora_repository_reports_data_' . $report_type;
     \Drupal::cache()->set($cid, $format_counts);
     $this->logger()->notice(dt('Cache built for media formats report data @report_type.', ['@report_type' => $report_type]));
  }

  /**
   * @param string $report_type
   *   The type of report (e.g., 'mimetype', 'model', 'puid').
   *
   * @command islandora_repository_reports:delete_cache
   * @usage islandora_repository_reports:delete_cache mimetype
   */
   public function delete_cache($report_type) {
     $utilities = \Drupal::service('islandora_repository_reports.utilities');
     $services = $utilities->getServices(TRUE);

     if (!in_array($report_type, $services)) {
       $this->logger()->error(dt('Report type @report_type not found.', ['@report_type' => $report_type]));
       exit();
     }

     $cid = 'islandora_repository_reports_data_' . $report_type;
     \Drupal::cache()->delete($cid);
     $this->logger()->notice(dt('Cache deleted for media formats report data @report_type.', ['@report_type' => $report_type]));
  }
}
