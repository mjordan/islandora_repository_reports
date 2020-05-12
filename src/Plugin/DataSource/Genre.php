<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets node by Islandra model.
 */
class Genre implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Returns the data source's name.
   *
   * @return string
   *   The name of the data source.
   */
  public function getName() {
    return t('Genre');
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
   * Returns the data source's chart title.
   *
   * @return string
   */
  public function getChartTitle() {
    return '@total uses of the Genre taxonomy.';
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
      ->groupBy('field_genre')
      ->aggregate('field_genre', 'COUNT')
      ->execute();
    $genre_counts = [];
    foreach ($result as $genre) {
      if ($term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($genre['field_genre_target_id'])) {
        $genre_counts[$term->label()] = $genre['field_genre_count'];
      }
    }
    return $genre_counts;
  }
}
