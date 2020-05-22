<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes by Islandora collection.
 */
class Collection implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Nodes by Islandora collection');
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
  public function getChartTitle() {
    return '@total total nodes broken down by collection.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy('field_member_of')
      ->aggregate('field_member_of', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->execute();
    $collection_counts = [];
    foreach ($result as $collection) {
      if (!is_null($collection['field_member_of_target_id'])) {
        if ($collection_node = \Drupal::entityTypeManager()->getStorage('node')->load($collection['field_member_of_target_id'])) {
          $collection_counts[$collection_node->getTitle()] = $collection['field_member_of_count'];
        }
      }
    }
    return $collection_counts;
  }
}
