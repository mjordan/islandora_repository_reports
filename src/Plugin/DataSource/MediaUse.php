<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source that gets media counts by Media Use terms.
 */
class MediaUse implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Media by Islandora Media Use');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return 'media';
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
    return t('@total media, grouped by Islandora Media Use term.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');

    $entity_type_manager = \Drupal::service('entity_type.manager');
    $media_storage = $entity_type_manager->getStorage('media');
    $result = $media_storage->getAggregateQuery()
      ->accessCheck(TRUE)
      ->groupBy('field_media_use')
      ->aggregate('field_media_use', 'COUNT')
      ->execute();
    $media_use_counts = [];
    foreach ($result as $use) {
      if (!is_null(($use['field_media_use_target_id']))) {
        if ($term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($use['field_media_use_target_id'])) {
          $media_use_counts[$term->label()] = $use['field_media_use_count'];
        }
      }
    }

    $this->csvData = [[t('Media Use term'), 'Count']];
    foreach ($media_use_counts as $media_use => $count) {
      $this->csvData[] = [$media_use, $count];
    }

    return $media_use_counts;
  }

}
