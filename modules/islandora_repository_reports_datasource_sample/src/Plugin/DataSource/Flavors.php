<?php

namespace Drupal\islandora_repository_reports_datasource_sample\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Sample data source for the Islandora Repository Reports module.
 */
class Flavors implements IslandoraRepositoryReportsDataSourceInterface{

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Flavors (sample)');
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
    return '@total flavors in this sample.';
  }

  /**
   * Gets the data.
   *
   * @return array
   *   An assocative array containing dataLabel => count members.
   *
   *   The data returned by this sample method is hard-coded, but the data
   *   in a custom plugin could come from a specific field on Media (see
   *   the MIME Type plugin), or nodes (see the NodesByMonth for an example
   *   of how to do that), from a Solr or Blazegraph query, or from some
   *   other data source.
   */
  public function getData() {
    return [
      'Spicy' => 100,
      'Sweet' => 20,
      'Salty' => 56,
      'Bitter' => 82,
      'Sour' => 5,
    ];
  }

}
