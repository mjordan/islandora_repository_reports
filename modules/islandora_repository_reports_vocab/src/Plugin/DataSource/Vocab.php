<?php

namespace Drupal\islandora_repository_reports_vocab\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Data source plugin that gets nodes by Islandora genre.
 */
class Vocab implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Nodes by terms in vocabulary');
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
    return t('@total uses of terms in the selected vocabulary in nodes of the selected content type.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    if (count($utilities->getSelectedContentTypes()) == 0) {
      return [];
    }

    $selected_content_type = '';
    $selected_vocabulary = '';
    if ($tempstore = \Drupal::service('tempstore.private')->get('islandora_repository_reports')) {
      if ($form_state = $tempstore->get('islandora_repository_reports_report_form_values')) {
        // Even though the content type form widget is checkboxes, we can
        // only use one value in the query below, so we take the first one
        // if multiple checked values exist.
        $selected_content_type_id = array_shift($form_state->getValue('islandora_repository_reports_content_types'));
        $selected_vocabulary_id = $form_state->getValue('islandora_repository_reports_vocabulary');
      }
    }

    $start_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range_start', '');
    $start_of_range = strlen($start_of_range) ? $start_of_range : $utilities->defaultStartDate;
    $start_of_range = trim($start_of_range);
    $end_of_range = $utilities->getFormElementDefault('islandora_repository_reports_nodes_by_month_range_end', '');
    $end_of_range = strlen($end_of_range) ? $end_of_range : $utilities->defaultEndDate;
    $end_of_range = trim($end_of_range);

    $field_vocab_pairs = [];
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $selected_content_type_id);
    foreach ($field_definitions as $field_name => $field_definition) {
      $settings = $field_definition->getSettings();
      if (array_key_exists('handler', $settings) && $settings['handler'] == 'default:taxonomy_term') {
        // Target_bundles is an array, so get the first vocabulary listed.
        $linked_vocab = array_shift($settings['handler_settings']['target_bundles']);
        $field_vocab_pairs[$linked_vocab] = $field_name;
      }
    }

    if (!array_key_exists($selected_vocabulary_id, $field_vocab_pairs)) {
      $this->csvData = [[t('Term'), 'Count']];
      return [];
    }

    $target_field = $field_vocab_pairs[$selected_vocabulary_id];
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $result = $node_storage->getAggregateQuery()
      ->groupBy($target_field)
      ->aggregate($target_field, 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->condition('created', $utilities->monthsToTimestamps($start_of_range, $end_of_range), 'BETWEEN')
      ->execute();
    $term_counts = [];
    foreach ($result as $field_value) {
      if (!is_null(($field_value[$target_field . '_target_id']))) {
        if ($term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($field_value[$target_field . '_target_id'])) {
          $term_counts[$term->label()] = $field_value[$target_field . '_count'];
        }
      }
    }

    $this->csvData = [[t('Term'), 'Count']];
    foreach ($term_counts as $field_value => $count) {
      $this->csvData[] = [$field_value, $count];
    }

    return $term_counts;
  }

}
