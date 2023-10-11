<?php

namespace Drupal\islandora_repository_reports_examples\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Sample tabular data source for the Islandora Repository Reports module.
 */
class SampleTable implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Example: Tabular report');
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
    return 'html';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle($total) {
    return t('Sample tabular report showing registered RDF namespaces');
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    // Get some data to put in the table.
    $namespaces = rdf_get_namespaces();

    $table_header = [t('Namespace alias'), t('Namespace URI')];
    $table_rows = [];
    foreach ($namespaces as $alias => $namespace_uri) {
      $table_rows[] = [$alias, $namespace_uri];
    }

    $this->csvData[] = ['Namespace alias', 'Namespace URI'];
    foreach ($table_rows as $row) {
      $this->csvData[] = $row;
    }
    // Unlike Chart.js reports, HTML reports need to call the writeCsvFile()
    // method explicitly here in getData().
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $utilities->writeCsvFile('sample_table', $this->csvData);

    // Reports of type 'html' return a render array, not raw data.
    return [
      '#theme' => 'table',
      '#header' => $table_header,
      '#rows' => $table_rows,
    ];
  }

}
