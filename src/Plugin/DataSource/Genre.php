<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes by Islandora genre.
 */
class Genre implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Nodes by genre');
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
    return '@total uses of the Genre taxonomy.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy('field_genre')
      ->aggregate('field_genre', 'COUNT')
      ->execute();
    $genre_counts = [];
    foreach ($result as $genre) {
      if (!is_null(($genre['field_genre_target_id']))) {
        if ($term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($genre['field_genre_target_id'])) {
          $genre_counts[$term->label()] = $genre['field_genre_count'];
        }
      }
    }
    return $genre_counts;
  }
}
