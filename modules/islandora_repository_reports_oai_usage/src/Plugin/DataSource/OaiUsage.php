<?php

namespace Drupal\islandora_repository_reports_oai_usage\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Sample data source for the Islandora Repository Reports module.
 */
class OaiUsage implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('OAI-PMH harvests');
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
    return t('@total harvests.', ['@total' => $total]);
  }

  /**
   * Gets the data.
   *
   * @return array
   *   An assocative array containing dataLabel => count members.
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');

    $database = \Drupal::database();
    $result = $database->query("SELECT created FROM {islandora_repository_reports_oai_usage_requests}");

    $harvest_counts = [];
    foreach ($result as $row) {
      $label = date("Y-m", $row->created);
      // if ($label >= $start_of_range) {
        if (array_key_exists($label, $harvest_counts)) {
          $harvest_counts[$label]++;
        }
        else {
          $harvest_counts[$label] = 1;
        }
      // }
    }

    // @todo: Even if we don't include hostnames in the chart, we should include them in the CSV.
    $this->csvData = [[t('Month'), 'Count']];
    foreach ($harvest_counts as $month => $count) {
      $this->csvData[] = [$month, $count];
    }

    return $harvest_counts;
  }

}
