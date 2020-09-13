<?php

namespace Drupal\islandora_repository_reports_examples\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source for the Islandora Repository Reports module.
 */
class PeopleInSpace implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Array of arrays corresponding to CSV records.
   *
   * @var string
   */
  public $csvData;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Example: List of people currently in space');
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
    return t('@total people are currently in space, according to http://open-notify.org.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $endpoint = 'http://api.open-notify.org/astros.json';
    $client = \Drupal::httpClient();
    $response = $client->request('GET', $endpoint, ['http_errors' => FALSE]);
    $response_output = (string) $response->getBody();
    $response_output = json_decode($response_output, TRUE);

    $data = [];
    foreach ($response_output['people'] as $astro) {
      $label = $astro['name'] . ' (' . $astro['craft'] . ')';
      // We aren't counting anything in this report,
      // so we just add 1 to every name.
      $data[$label] = 1;
    }

    $this->csvData = [[t('Name and craft'), 'Number of people']];
    foreach ($data as $label => $count) {
      $this->csvData[] = [$label, $count];
    }

    return $data;
  }

}
