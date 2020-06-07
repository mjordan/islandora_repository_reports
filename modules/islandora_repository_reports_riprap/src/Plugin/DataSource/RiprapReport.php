<?php

namespace Drupal\islandora_repository_reports_riprap\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Sample tabular data source for the Islandora Repository Reports module.
 */
class RiprapReport implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Failed fixity check events');
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
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    // Reports of type 'html' return a render array, not raw data.
    $link = Link::fromTextAndUrl(
      t('fixity check report'),
      Url::fromUri('internal:/admin/reports/islandora_riprap')
    );
    return [
      '#children' => '<div>' .
      t('The @link is available in the main Drupal reports list.', ['@link' => $link->toString()]) .
      '</div>',
    ];
  }

}
