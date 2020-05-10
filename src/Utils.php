<?php

namespace Drupal\islandora_repository_reports;

/**
 * Utilities for the Media Formats Reports module.
 */
class Utils {

  /**
   * Gets a list of data source services that can be used in the report selector form.
   *
   * @param bool $ids_only
   *    Whether to return an array of service IDs instead of an associative array of ids/names.
   *
   * @return array
   *   Associative array of services.
   */
  public function getServices($ids_only = FALSE) {
    $container = \Drupal::getContainer();
    $services = $container->getServiceIds();
    $services = preg_grep("/islandora_repository_reports\.datasource\./", $services);

    if ($ids_only) {
      $service_ids_to_return = [];
      foreach ($services as $service_id) {
        $service_id = preg_replace('/^.*datasource\./', '', $service_id);
        $service_ids_to_return[] = $service_id;
      }
      return $service_ids_to_return;
    }

    $options = [];
    foreach ($services as $service_id) {
      $service = \Drupal::service($service_id);
      $service_id = preg_replace('/^.*datasource\./', '', $service_id);
      $options[$service_id] = $service->getName();
    }
    return $options;
  }

  /**
   * Generate a set of random colors to use in the chart.
   *
   * @param int $length
   *   The length of the array to generate.
   *
   * @return array
   *    An array of RGB values in the format required by Chart.js, e.g.,
   *    array('rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(255, 206, 86)').
   */
  public function getChartColors($length) {
    $colors = [];
    for ($i = 1; $i <= $length; $i++) {
      $rgb_color = [];
      foreach (['r', 'g', 'b'] as $color) {
        $rgb_color[$color] = rand(0, 255);
      }
      $colors[] = 'rgba(' . implode(',', $rgb_color) . ')';
    }
    return $colors;
  }

}
