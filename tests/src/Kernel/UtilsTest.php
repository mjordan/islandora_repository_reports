<?php

namespace Drupal\Tests\islandora_repository_reports\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the Utilities service.
 *
 * @group islandora_repository_reports
 */
class UtilsTest extends KernelTestBase {

  /**
   * The service under test.
   *
   * @var \Drupal\islandora_repository_reports\Utils
   */
  protected $utilsService;

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = ['islandora_repository_reports'];

  /**
   * Tests generating random colors.
   */
  public function testRandomColors() {
    $this->installConfig(['islandora_repository_reports']);
    $this->utilsService = \Drupal::service('islandora_repository_reports.utilities');
    $this->assertCount(5, $this->utilsService->getRandomChartColors(5));
  }

  /**
   * Tests the list of services.
   */
  public function testServices() {
    $this->installConfig(['islandora_repository_reports']);
    $this->utilsService = \Drupal::service('islandora_repository_reports.utilities');
    $this->assertEquals([
      'mimetype',
      'media_use',
      'model',
      'genre',
      'content_type',
      'nodes_by_month',
      'disk_usage',
      'collection',
    ], $this->utilsService->getServices(TRUE));
  }

}
