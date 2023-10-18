<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets nodes by Islandora collection.
 */
class Collection implements IslandoraRepositoryReportsDataSourceInterface {

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
  public function getChartTitle($total) {
    return t('@total total nodes broken down by collection.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    if (count($utilities->getSelectedContentTypes()) == 0) {
      return [];
    }

    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->accessCheck(TRUE)
      ->groupBy('field_member_of')
      ->aggregate('field_member_of', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->execute();
    $collection_counts = [];
    foreach ($result as $collection) {
      if (!is_null($collection['field_member_of_target_id'])) {
        if ($collection_node = \Drupal::entityTypeManager()->getStorage('node')->load($collection['field_member_of_target_id'])) {
          if ($utilities->nodeIsCollection($collection_node)) {
            $collection_counts[$collection_node->getTitle()] = $collection['field_member_of_count'];
          }
        }
      }
    }

    $this->csvData = [[t('Collection'), 'Count']];
    foreach ($collection_counts as $collection => $count) {
      $this->csvData[] = [$collection, $count];
    }

    return $collection_counts;
  }

}
