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

    $field_name = $utilities->getFormElementDefault('islandora_repository_reports_field_values_field_name', '');

    $changed_date_range = $utilities->monthsToTimestamps($start_of_range, $end_of_range);

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->condition('changed', $changed_date_range, 'BETWEEN');
    $nids = $query->execute();
    $nodes = $node_storage->loadMultiple($nids);

    $value_counts = [];
    $table_header = [
      t('Values in @field', ['@field' => $field_name]),
      t('Number of occurances'),
    ];
    $table_rows = [];
    foreach ($nodes as $node) {
      try {
        $field_type = $node->get($field_name)->getFieldDefinition()->getType();
      }
      catch (\Exception $e) {
        \Drupal::messenger()->addWarning(t("Field '@field_name' does not exist.", ['@field_name' => $field_name]));
      }
      if ($node->hasField($field_name)) {
        // If we add additional field types, we will need to add logic here to
        // get their values.
        $field_values = $node->get($field_name)->getValue();
        if (count($field_values) > 0) {
          foreach ($field_values as $field_value) {
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

    if (count($value_counts) === 0) {
      \Drupal::messenger()->addWarning(t("No instances of '@field_name' found, using the search options below.", ['@field_name' => $field_name]));
      return [];
    }

    // Sort most to least frequent.
    arsort($value_counts);
    foreach ($value_counts as $value => $count) {
      $table_rows[] = [$value, $count];
    }

    $this->csvData[] = ['Values in ' . $field_name, 'Number of occurances'];
    foreach ($value_counts as $value => $count) {
      $this->csvData[] = [$value, $count];
    }
    // Unlike Chart.js reports, HTML reports need to call the writeCsvFile()
    // method explicitly here in getData().
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $utilities->writeCsvFile('field_value', $this->csvData);

    // Reports of type 'html' return a render array, not raw data.
    // @todo: if count($field_values) is 0, tell the user.
    return [
      '#theme' => 'table',
      '#header' => $table_header,
      '#rows' => $table_rows,
      // Sanitize the outgoing markup.
      '#type' => 'processed_text',
    ];
  }

}
