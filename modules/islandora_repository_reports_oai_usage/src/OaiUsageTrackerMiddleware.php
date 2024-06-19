<?php

namespace Drupal\islandora_repository_reports_oai_usage;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Database;

/**
 * OaiUsageTrackerMiddleware class.
 */
class OaiUsageTrackerMiddleware implements HttpKernelInterface {
  /**
   * The kernel implementation.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  private $app;

  /**
   * Create a new OaiUsageTrackerMiddleware instance.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
   *   Http Kernel.
   */
  public function __construct(HttpKernelInterface $app) {
    $this->app = $app;
  }

  /**
   * {@inheritDoc}
   */
  public function handle(Request $request, $type = self::MAIN_REQUEST, bool $catch = TRUE): Response {
    $current_uri = $request->getRequestUri();
    // @todo: Use the REST OAI-PMH module's repository_path config value.
    // \Drupal::config('rest_oai_pmh.settings')->get('repository_path')
    // should work (that syntax works for other configs) but it's coming back
    // NULL in all cases. For now, we define our own config setting.
    // See https://github.com/mjordan/islandora_repository_reports/issues/55.
    $config = \Drupal::config('islandora_repository_reports_oai_usage.settings');
    $oai_endpoint = $config->get('islandora_repository_reports_oai_usage_endpoint');
    $list_records_request = $oai_endpoint . '?verb=ListRecords';
    if (strpos($current_uri, $list_records_request) === 0) {
      $ip_address = $request->getClientIp();
      // Get hostname when there is one, otherwise just
      // use harvester's IP address.
      $hostname = gethostbyaddr($ip_address);
      $hostname = $hostname ? $hostname : '';

      $database = Database::getConnection();
      $result = $database->insert('islandora_repository_reports_oai_usage_requests')
        ->fields([
          'ip_address' => $ip_address,
          'hostname' => $hostname,
          'created' => \Drupal::time()->getRequestTime(),
          'request' => $request->getRequestUri(),
        ])
        ->execute();
    }
    return $this->app->handle($request, $type, $catch);
  }

}
