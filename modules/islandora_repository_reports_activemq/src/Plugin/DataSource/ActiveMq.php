<?php

namespace Drupal\islandora_repository_reports_activemq\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * ActiveMQ data source for the Islandora Repository Reports module.
 */
class ActiveMq implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Pending ActiveMQ messages');
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
    return t('@total total pending messages.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $config = \Drupal::config('islandora_repository_reports_activemq.settings');

    $username = $config->get('islandora_repository_reports_activemq_admin_username');
    $password = $config->get('islandora_repository_reports_activemq_admin_password');
    $auth = 'Basic ' . base64_encode($username . ':' . $password);
    $endpoint = $config->get('islandora_repository_reports_activemq_admin_console_url');

    $client = \Drupal::httpClient();
    $response = $client->request('GET', $endpoint, ['http_errors' => FALSE, 'headers' => ['Authorization' => $auth]]);
    $html_content = (string) $response->getBody();
    if ($response->getStatusCode() != 200) {
      \Drupal::logger('islandora_repository_reports_activemq')->info(t("ActiveMQ admin console requres returned response code @code.", ['@code' => $response->getStatusCode()]));
      \Drupal::messenger()->addStatus(t('Cannot connect to the ActiveMQ admin console. Addtional informaiton is available in the system log.'));
      return [];
    }

    $dom = new \DOMDocument();
    @$dom->loadHTML($html_content);
    $tables = $dom->getElementsByTagName('table');
    $rows = $tables->item(1)->getElementsByTagName('tr');
    $queue_counts = [];
    foreach ($rows as $row) {
      $cols = $row->getElementsByTagName('td');
      if (is_object($cols->item(0))) {
        $queue_counts[trim($cols->item(0)->nodeValue)] = trim($cols->item(1)->nodeValue);
      }
    }

    $this->csvData = [[t('Random data point'), 'Count']];
    foreach ($queue_counts as $label => $count) {
      $this->csvData[] = [$label, $count];
    }

    return $queue_counts;
  }

}
