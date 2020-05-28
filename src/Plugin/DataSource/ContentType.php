<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets nodes by Drupal content type.
 */
class ContentType implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Nodes by content type');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return 'node';
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
    return t('@total nodes broken down by content type.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy('type')
      ->aggregate('type', 'COUNT')
      ->execute();
    $type_counts = [];
    foreach ($result as $type) {
      $type_counts[$type['type']] = $type['type_count'];
    }

    $this->csvData = [[t('Content type'), 'Count']];
    foreach ($type_counts as $type => $count) {
      $this->csvData[] = [$type, $count];
    }

    return $type_counts;
  }

}
