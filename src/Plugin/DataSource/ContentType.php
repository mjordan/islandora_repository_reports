<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes by Drupal content type.
 */
class ContentType implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Nodes by content type');
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
  public function getChartTitle() {
    return '@total nodes broken down by content type.';
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
    return $type_counts;
  }
}
