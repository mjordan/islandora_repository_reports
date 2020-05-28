<?php

namespace Drupal\islandora_repository_reports_puid\Plugin\DataSource;

/**
 * Data source that gets media counts by PUID.
 */
class Puid implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Media by PRONOM PUID');
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
    return t('@total media grouped by PUID.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $media_storage = $entity_type_manager->getStorage('media');
    // We don't include a condition/filter, we just get all the
    // unique values in the db table.
    $result = $media_storage->getAggregateQuery()
      ->groupBy('fits_droid_puid')
      ->aggregate('fits_droid_puid', 'COUNT')
      ->execute();
    $format_counts = [];
    foreach ($result as $format) {
      if (strlen($format['fits_droid_puid_value'])) {
        $format_counts[$format['fits_droid_puid_value']] = $format['fits_droid_puid_count'];
      }
    }

    $this->csvData = [[t('PRONOM ID'), 'Count']];
    foreach ($format_counts as $label => $count) {
      $this->csvData[] = [$label, $count];
    }

    return $format_counts;
  }

}
