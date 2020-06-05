<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets nodes by Islandora genre.
 */
class Genre implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Array of arrays corresponding to CSV records.
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
  public function getChartTitle($total) {
    return t('@total uses of the Genre taxonomy.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $start_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range_start', '');
    $start_of_range = trim($start_of_range);
    $end_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range_end', '');
    $end_of_range = trim($end_of_range);

    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy('field_genre')
      ->aggregate('field_genre', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->condition('created', $utilities->monthsToTimestamps($start_of_range, $end_of_range), 'BETWEEN')
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
