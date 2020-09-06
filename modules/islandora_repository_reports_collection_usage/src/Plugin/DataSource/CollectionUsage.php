<?php

namespace Drupal\islandora_repository_reports_collection_usage\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes by Islandora collection.
 */
class CollectionUsage implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Usage of Islandora collections');
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
    return t('Collection "views", derived from the sum of all Matomo node views within each collection.');
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
      ->groupBy('field_member_of')
      ->aggregate('field_member_of', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->execute();
    $collection_counts = [];
    foreach ($result as $collection) {
      if (!is_null($collection['field_member_of_target_id'])) {
        if ($collection_node = \Drupal::entityTypeManager()->getStorage('node')->load($collection['field_member_of_target_id'])) {
          if ($utilities->nodeIsCollection($collection_node)) {
            // Get all nodes that have the current collection node ID as
            // a value in field_member_of.
            $node_ids_query = \Drupal::entityQuery('node')
              ->condition('field_member_of', $collection['field_member_of_target_id']);
            $node_ids_result = $node_ids_query->execute();
            $nids = array_values($node_ids_result);
            $collection_views = 0;
            foreach ($nids as $nid) {
              if ($nid) {
                $node_views = \Drupal::service('islandora_matomo.default')->getViewsForNode(['nid' => $nid, 'end_date' => date('Y-m-d')]);
                $collection_views = $collection_views + $node_views;
              }
            }
            $collection_counts[$collection_node->getTitle()] = $collection_views;
          }
        }
      }
    }

    $this->csvData = [[t('Collection'), 'Views']];
    foreach ($collection_counts as $collection => $count) {
      $this->csvData[] = [$collection, $count];
    }

    return $collection_counts;
  }

}
