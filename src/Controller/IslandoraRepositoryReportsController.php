<?php

namespace Drupal\islandora_repository_reports\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller.
 */
class IslandoraRepositoryReportsController extends ControllerBase {

  /**
   * Output the report.
   *
   * The chart itself is rendered via Javascript.
   *
   * @return string
   *   Markup used by the chart.
   */
  public function main() {
    if ($tempstore = \Drupal::service('tempstore.private')->get('islandora_repository_reports')) {
      $show_csv_link = $tempstore->get('islandora_repository_reports_generate_csv');
    }
    $form = \Drupal::formBuilder()->getForm('Drupal\islandora_repository_reports\Plugin\Form\IslandoraRepositoryReportsReportSelectorForm');
    return [
      '#form' => $form,
      '#show_csv_link' => $show_csv_link,
      '#theme' => 'islandora_repository_reports_chart',
    ];
  }

}
