<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets disk usage by Drupal filesystem.
 */
class DiskUsage implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * Variable $csvData is an array of arrays corresponding to CSV records.
   *
   * @var string
   */
  public $csvData;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Disk usage');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return NULL;
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
    return '@total GB total disk usage.';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $disk_usage_type = $utilities->getFormElementDefault('islandora_repository_reports_disk_usage_type', 'filesystem');

    $database = \Drupal::database();
    $result = $database->query("SELECT uri, filesize, filemime FROM {file_managed}");

    $filesystem_usage = [];
    if ($disk_usage_type == 'filesystem') {
      foreach ($result as $row) {
        $filesystem = strtok($row->uri, ':');
        if (array_key_exists($filesystem, $filesystem_usage)) {
          $filesystem_usage[$filesystem] = $filesystem_usage[$filesystem] + $row->filesize;
        }
        else {
          $filesystem_usage[$filesystem] = $row->filesize;
        }
      }
    }

    if ($disk_usage_type == 'mimetype') {
      foreach ($result as $row) {
        if (array_key_exists($row->filemime, $filesystem_usage)) {
          $filesystem_usage[$row->filemime] = $filesystem_usage[$row->filemime] + $row->filesize;
        }
        else {
          $filesystem_usage[$row->filemime] = $row->filesize;
        }
      }
    }

    if ($disk_usage_type == 'collection') {
      $filesystem_usage_cid = [];
      $result = $database->query("SELECT {node__field_member_of}.field_member_of_target_id AS collection_id,
	{media__field_file_size}.field_file_size_value AS filesize FROM {node__field_member_of},
	{media__field_file_size}, {media__field_media_of} WHERE
	{media__field_file_size}.entity_id = {media__field_media_of}.entity_id AND
	{node__field_member_of}.entity_id = {media__field_media_of}.field_media_of_target_id");

      foreach ($result as $row) {
        if (is_numeric($row->collection_id)) {
          if ($collection_node = \Drupal::entityTypeManager()->getStorage('node')->load($row->collection_id)) {
            if (!$utilities->nodeIsCollection($collection_node)) {
              continue;
            }
          }
          else {
            continue;
          }
        }
        else {
          continue;
        }

        if (array_key_exists($row->collection_id, $filesystem_usage_cid)) {
          $filesystem_usage_cid[$row->collection_id] = $filesystem_usage_cid[$row->collection_id] + $row->filesize;
        }
        else {
          $filesystem_usage_cid[$row->collection_id] = $row->filesize;
        }
      }

      $filesystem_usage = [];
      foreach ($filesystem_usage_cid as $collection_id => $disk_usage) {
        if ($collection_node = \Drupal::entityTypeManager()->getStorage('node')->load($collection_id)) {
          $filesystem_usage[$collection_node->getTitle()] = $disk_usage;
        }
      }
    }

    // Drupal gives us bytes, so we convert to GB.
    $converted_filesystem_usage = [];
    foreach ($filesystem_usage as $key => $usage) {
      $converted_filesystem_usage[$key] = round($usage / 1024 / 1024 / 1024, 4);
    }

    $this->csvData = [[t('Type of disk usage'), 'Usage']];
    foreach ($converted_filesystem_usage as $type => $usage) {
      $this->csvData[] = [$type, $usage . ' GB'];
    }

    return $converted_filesystem_usage;
  }

}
