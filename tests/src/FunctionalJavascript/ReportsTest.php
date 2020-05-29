<?php

namespace Drupal\Tests\islandora_repository_reports\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests Islandora Repository Reports functionality.
 *
 * @group islandora_repository_reports
 */
class ReportsTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['islandora_repository_reports'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create a user with permissions to manage.
    $permissions = [
      'administer site configuration',
    ];
    $account = $this->drupalCreateUser($permissions);

    // Initiate user session.
    $this->drupalLogin($account);
  }

  /**
   * Tests for some text on the reports page.
   */
  public function testReports() {
    $this->drupalGet('admin/reports/islandora_repository_reports');
    // "No @name data available to report on." will appear before the
    // user selects a report.
    $this->assertSession()->pageTextContains('data available to report on');
  }

}
