<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source plugin that gets nodes by Islandora genre.
 */
class FieldValue implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Unique values in a node field');
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
    // return 'pie';
    return 'html';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle($total) {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    if (count($utilities->getSelectedContentTypes()) == 0) {
      return [];
    }

    $start_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range_start', '');
    $start_of_range = strlen($start_of_range) ? $start_of_range : $utilities->defaultStartDate;
    $start_of_range = trim($start_of_range);
    $end_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range_end', '');
    $end_of_range = strlen($end_of_range) ? $end_of_range : $utilities->defaultEndDate;
    $end_of_range = trim($end_of_range);

    $changed_date_range = $utilities->monthsToTimestamps($start_of_range, $end_of_range);

    $field_name = 'field_extent';
    // $field_name = 'field_identifier';

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->condition('changed', $changed_date_range, 'BETWEEN');
    $nids = $query->execute();
    $nodes = $node_storage->loadMultiple($nids);

    $value_counts = [];
    $table_header = [t('Values in @field', ['@field' => $field] ), t('Number of occurances')];
    $table_rows = [];
    // For now, only support specific field types. Add more later. See comment below.
    $allowed_field_types = ['string', 'string_long', 'text', 'text_long'];
    foreach ($nodes as $node) {
      $field_type = $node->get($field_name)->getFieldDefinition()->getType();
      if ($node->hasField($field_name) && in_array($field_type, $allowed_field_types)) {
	$field_values = $node->get($field_name)->getValue();
        if (count($field_values) > 0) {
          foreach ($field_values as $field_value) {
	    // Differnt field types will require different ways of accessing values.
	    if (isset($value_counts[$field_value['value']])) {
              $value_counts[$field_value['value']]++;
	    }
	    else {
              $value_counts[$field_value['value']] = 1;
	    }
	  }
	}
      }
    }

    arsort($value_counts);
    foreach ($value_counts as $value => $count) {
      $table_rows[] = [$value, $count];
    }

    $this->csvData[] = ['Values in ' . $field, 'Number of occurances'];
    foreach ($value_counts as $value => $count) {
      $this->csvData[] = [$value, $count];
    }
    // Unlike Chart.js reports, HTML reports need to call the writeCsvFile()
    // method explicitly here in getData().
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $utilities->writeCsvFile('field_value', $this->csvData);

    // Reports of type 'html' return a render array, not raw data.
    return [
      '#theme' => 'table',
      '#header' => $table_header,
      '#rows' => $table_rows,
    ];

    return $value_counts;
  }

}
