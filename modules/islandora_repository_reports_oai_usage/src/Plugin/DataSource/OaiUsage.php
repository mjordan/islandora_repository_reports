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
    $start_of_range = $utilities->getFormElementDefault('islandora_repository_reports_oai_usage_by_month_range_start', '');
    $start_of_range = strlen($start_of_range) ? $start_of_range : $utilities->defaultStartDate;
    $start_of_range = trim($start_of_range);
    $end_of_range = $utilities->getFormElementDefault('islandora_repository_reports_oai_usage_by_month_range_end', '');
    $end_of_range = strlen($end_of_range) ? $end_of_range : $utilities->defaultEndDate;
    $end_of_range = trim($end_of_range);

    $database = \Drupal::database();
    $sql = "SELECT * FROM {islandora_repository_reports_oai_usage_requests}";
    $result = $database->query($sql);

    $harvest_counts = [];
    foreach ($result as $row) {
      if (strpos($row->request, 'ListRecords') && !strpos($row->request, 'resumptionToken')) {
        $label = date("Y-m", $row->created);
        if ($label >= $start_of_range && $label <= $end_of_range) {
          if (array_key_exists($label, $harvest_counts)) {
            $harvest_counts[$label]++;
          }
          else {
            $harvest_counts[$label] = 1;
          }
        }
      }
    }

    // @todo: Include hostnames in the CSV so users know who is harvesting them.
    $this->csvData = [[t('Month'), 'Count']];
    foreach ($harvest_counts as $month => $count) {
      $this->csvData[] = [$month, $count];
    }

    return $harvest_counts;
  }

}
