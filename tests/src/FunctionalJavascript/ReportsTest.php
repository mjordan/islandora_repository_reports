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
  public static $modules = [
    'islandora_repository_reports',
    'islandora_repository_reports_datasource_random_bar',
  ];

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
    $web_assert = $this->assertSession();
    $page = $this->getSession()->getPage();
    $report_dropdown = $assert->fieldExists('islandora_repository_reports_report_type');
    $report_dropdown->setValue('random_bar');
    $button = $page->findButton('Go');
    $this->assertSession()->pageTextContains('Random data for bar charts');
  }

}
