<?php

namespace Drupal\islandora_repository_reports_matomo\Plugin\DataSource;

use Drupal\islandora_repository_reports\Plugin\DataSource\IslandoraRepositoryReportsDataSourceInterface;

/**
 * Sample tabular data source for the Islandora Repository Reports module.
 */
class Matomo implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Matomo dashboard');
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
    return t('Matomo dashboard');
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $config = \Drupal::config('matomo.settings');
    $matomo_url = strlen($config->get('url_https')) ? $config->get('url_https') : $config->get('url_http'); 
    $matomo_dashboard = '<iframe src="' . $matomo_url;
    $matomo_dashboard .= '/index.php?module=Widgetize&action=iframe&';
    $matomo_dashboard .= 'moduleToWidgetize=Dashboard&';
    $matomo_dashboard .= 'actionToWidgetize=index&idSite=';
    $matomo_dashboard .= $config->get('site_id');
    $matomo_dashboard .= '&period=week&date=yesterday" frameborder="0"';
    $matomo_dashboard .= 'marginheight="0" marginwidth="0" width="100%"';
    $matomo_dashboard .= 'height="500px"></iframe>';
    return [
      '#children' => $matomo_dashboard,
    ];
  }

}
