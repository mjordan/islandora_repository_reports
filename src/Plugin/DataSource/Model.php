<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets nodes by Islandora model.
 */
class Model implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Nodes by Islandora Model');
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
    return t('@total total nodes broken down by Islandora model.', ['@total' => $total]);
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
      ->groupBy('field_model')
      ->aggregate('field_model', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->execute();
    $model_counts = [];
    foreach ($result as $model) {
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($model['field_model_target_id']);
      if ($term && method_exists($term, 'label')) {
        $model_counts[$term->label()] = $model['field_model_count'];
      }
    }

    $this->csvData = [[t('Islandora model'), 'Count']];
    foreach ($model_counts as $model => $count) {
      $this->csvData[] = [$model, $count];
    }

    return $model_counts;
  }

}
