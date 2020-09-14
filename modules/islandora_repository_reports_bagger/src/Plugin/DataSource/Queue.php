<?php

namespace Drupal\islandora_repository_reports_bagger\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
    $islandora_bagger_integration_config = \Drupal::config('islandora_bagger_integration.settings');

    $bagger_mode = $islandora_bagger_integration_config->get('islandora_bagger_mode');
    if ($mode == 'remote') {
      $endpoint = $config->get('islandora_repository_reports_bagger_queue_endpoint');
      $client = \Drupal::httpClient();
      $response = $client->request('GET', $endpoint, ['http_errors' => FALSE]);
      $response_output = (string) $response->getBody();
      $queue_contents = json_decode($response_output, TRUE);
      devel_debug($response);

      if (count($queue_contents) === 0) {
        \Drupal::messenger()->addWarning(t("The Islandora Bagger queue is empty."));
        return [];
      }
    }
    if ($mode == 'local') {
      $path_to_queue = $config->get('islandora_repository_reports_bagger_queue_local_path');
      $bagger_directory = $islandora_bagger_integration_config->get('islandora_bagger_local_bagger_directory');
      $bagger_cmd = ['./bin/console', 'app:islandora_bagger:get_queue', '--queue=' . $path_to_queue, '--output_format=json'];

      $process = new Process($bagger_cmd);
      $process->setWorkingDirectory($bagger_directory);
      $process->run();

      if ($process->isSuccessful()) {
        $queue_contents = $process->getOutput();
      }
      else {
        \Drupal::messenger()->addWarning(t("The Islandora Bagger queue is empty."));
        return [];
      }

      if (count($command_output) === 0) {
        \Drupal::messenger()->addWarning(t("The Islandora Bagger queue is empty."));
        return [];
      }
    }

    $table_header = [
      t('Node ID'),
      t('Config file path'),
    ];

    $table_rows = [];
    foreach ($queue_contents as $queue_entry) {
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
