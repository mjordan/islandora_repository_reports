<?php

namespace Drupal\islandora_repository_reports_examples\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Sample data source for the Islandora Repository Reports module.
 */
class Flavors implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Example: Flavors');
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
  public function getChartTitle($total) {
    return t('@total flavors in this sample.', ['@total' => $total]);
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
    $flavors = [
      'Spicy' => 100,
      'Sweet' => 20,
      'Salty' => 56,
      'Bitter' => 82,
      'Sour' => 5,
    ];

    $this->csvData = [[t('Flavor'), 'Count']];
    foreach ($flavors as $flavor => $count) {
      $this->csvData[] = [$flavor, $count];
    }

    return $flavors;
  }

}
