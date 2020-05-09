<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets node by Islandra model.
 */
class Model implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Returns the data source's name.
   *
   * @return string
   *   The name of the data source.
   */
  public function getName() {
    return t('Islandora Model');
  }

  /**
   * Returns the data source's chart type.
   *
   * @return string
   *   Either 'pie' or 'bar'.
   */
  public function getChartType() {
    return 'pie';
  }

  /**
   * Gets the data.
   *
   * @return array
   *   An assocative array containing formatlabel => count members. 
   */
  public function getData() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy('field_model')
      ->aggregate('field_model', 'COUNT')
      ->execute();
    $model_counts = [];
    foreach ($result as $model) {
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($model['field_model_target_id']);
      $model_counts[$term->label()] = $model['field_model_count'];
    }
    return $model_counts;
  }
}
