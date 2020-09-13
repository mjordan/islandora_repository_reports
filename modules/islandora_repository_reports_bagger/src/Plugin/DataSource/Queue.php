<?php

namespace Drupal\islandora_repository_reports_bagger\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source for the Islandora Repository Reports module.
 */
class Queue implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Islandora Bagger queue');
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
    return 'html';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle($total) {
    return t('Entries in the Islandora Bagger queue.');
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $config = \Drupal::config('islandora_repository_reports_bagger.settings');
    $endpoint = $config->get('islandora_repository_reports_bagger_queue_endpoint');

    // @todo: Getting the queue via HTTP should only happen if in remote mode.
    // If in local mode, run console command to get queue.
    $client = \Drupal::httpClient();
    $response = $client->request('GET', $endpoint, ['http_errors' => FALSE]);
    $response_output = (string) $response->getBody();
    $response_output = json_decode($response_output, TRUE);

    if (count($response_output) === 0) {
      \Drupal::messenger()->addWarning(t("The Islandora Bagger queue is empty."));
      return [];
    }

    $table_header = [
      t('Node ID'),
      t('Config file path'),
    ];

    $table_rows = [];
    foreach ($response_output as $queue_entry) {
      list($nid, $config_path) = explode('	', $queue_entry);
      $table_rows[] = [$nid, $config_path];
    }

    // Reports of type 'html' return a render array, not raw data.
    return [
      '#theme' => 'table',
      '#header' => $table_header,
      '#rows' => $table_rows,
      '#sticky' => TRUE,
    ];

  }

}
