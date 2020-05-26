<?php

namespace Drupal\islandora_repository_reports_rdfmappings\Plugin\DataSource;

use Drupal\Core\Render\Markup;
use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Random data source for the Islandora Repository Reports module.
 */
class RdfMappings implements IslandoraRepositoryReportsDataSourceInterface{

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
    return t('Drupal field to RDF property mappings');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return null;
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
  public function getChartTitle() {
    return t('Drupal field to RDF property mappings');
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $bundle_type = 'page';
    $entity_type = 'node';
    if ($tempstore = \Drupal::service('user.private_tempstore')->get('islandora_repository_reports')) {
      if ($form_state = $tempstore->get('islandora_repository_reports_report_form_values')) {
        $bundle_type = $form_state->getValue('islandora_repository_reports_rdf_mapping_bundle_type');
        $entity_type_list = unserialize($form_state->getValue('islandora_repository_reports_rdf_mapping_entity_type'));
        $entity_type = $entity_type_list[$bundle_type];
      }
    }

    $namespaces = rdf_get_namespaces();
    $namespaces_table_rows = [];
    foreach ($namespaces as $alias => $namespace_uri) {
      $namespaces_table_rows[] =  [$alias, $namespace_uri];
    }
    $namespaces_table_header = [t('Namespace alias'), t('Namespace URI')];

    $namespaces_table = [
      '#theme' => 'table',
      '#header' => $namespaces_table_header,
      '#rows' => $namespaces_table_rows,
    ];
    $namespaces_table_markup = \Drupal::service('renderer')->render($namespaces_table);

    $rdf_mappings = rdf_get_mapping($entity_type, $bundle_type);
    $fields = \Drupal::entityManager()->getFieldDefinitions($entity_type, $bundle_type);
    $mappings_table_rows = [];
    foreach ($fields as $field_name => $field_object) {
      $field_mappings = $rdf_mappings->getPreparedFieldMapping($field_name);
      if (array_key_exists('properties', $field_mappings)) {
        $mappings_table_rows[] = [$field_object->getLabel() . ' (' . $field_name . ')', $field_mappings['properties'][0]];
      }
    }

    $this->csvData[] = ['Drupal field', 'RDF property'];
    foreach ($mappings_table_rows as $mapping) {
      $this->csvData[] = $mapping;
    }
    // Unlike Chart.js reports, HTML reports need to call the writeCsvFile()
    // method explicitly here in getData().
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $utilities->writeCsvFile('rdf_mappings', $this->csvData);

    // Reports of type 'html' return rendered markup, not raw data.
    $mappings_header = [t('Drupal field'), t('RDF property')];
    return [
      '#theme' => 'table',
      '#header' => $mappings_header,
      '#rows' => $mappings_table_rows,
      '#prefix' => t('RDF namespaces are defined below. Mappings altered or created dynamically by modules are not reflected in this table.'),
      '#suffix' => $namespaces_table_markup, 
    ];
  }

}
