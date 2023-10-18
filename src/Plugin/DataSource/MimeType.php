<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source that gets media counts by MIME type.
 */
class MimeType implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Media by MIME type');
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
    return t('@total media with the selected Media Use terms, grouped by MIME type.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $media_use_term_ids = $utilities->getFormElementDefault('islandora_repository_reports_media_use_terms', []);

    if (count($media_use_term_ids) == 0) {
      return [];
    }

    $entity_type_manager = \Drupal::service('entity_type.manager');
    $media_storage = $entity_type_manager->getStorage('media');
    $result = $media_storage->getAggregateQuery()
      ->accessCheck(TRUE)
      ->groupBy('field_mime_type')
      ->aggregate('field_mime_type', 'COUNT')
      ->condition('field_media_use', $media_use_term_ids, 'IN')
      ->execute();
    $format_counts = [];
    foreach ($result as $format) {
      $format_counts[$format['field_mime_type']] = $format['field_mime_type_count'];
    }

    $this->csvData = [[t('MIME type'), 'Count']];
    foreach ($format_counts as $type => $count) {
      $this->csvData[] = [$type, $count];
    }

    return $format_counts;
  }

}
