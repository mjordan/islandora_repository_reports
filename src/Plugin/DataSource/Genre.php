<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes by Islandora genre.
 */
class Genre implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * $csvData is an array of arrays corresponding to CSV records.
   *
   * @var string
   */
  public $csvData;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Nodes by genre');
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
    return '@total uses of the Genre taxonomy.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy('field_genre')
      ->aggregate('field_genre', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->execute();
    $genre_counts = [];
    foreach ($result as $genre) {
      if (!is_null(($genre['field_genre_target_id']))) {
        if ($term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($genre['field_genre_target_id'])) {
          $genre_counts[$term->label()] = $genre['field_genre_count'];
        }
      }
    }

    $this->csvData = [[t('Genre'), 'Count']];
    foreach ($genre_counts as $genre => $count) {
      $this->csvData[] = [$genre, $count];
    }
 
    return $genre_counts;
  }
}
