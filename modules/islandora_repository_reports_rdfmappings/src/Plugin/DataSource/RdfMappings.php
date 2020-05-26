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

    $rdf_mappings = rdf_get_mapping('node', 'islandora_object');
    $fields = \Drupal::entityManager()->getFieldDefinitions('node', 'islandora_object');
    $mappings_table_rows = [];
    foreach ($fields as $field_name => $field_object) {
      $field_mappings = $rdf_mappings->getPreparedFieldMapping($field_name);
      if (array_key_exists('properties', $field_mappings)) {
        $mappings_table_rows[] = [$field_name, $field_mappings['properties'][0]];
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
      '#prefix' => t('Namespaces are defined below.'),
      '#suffix' => $namespaces_table_markup, 
    ];
  }

}
