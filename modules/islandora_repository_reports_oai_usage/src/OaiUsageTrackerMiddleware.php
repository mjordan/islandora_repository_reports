<?php

namespace Drupal\islandora_repository_reports_oai_usage;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

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
   * @param  \Symfony\Component\HttpKernel\HttpKernelInterface $app
   *   Http Kernel.
   */
  public function __construct(HttpKernelInterface $app) {
    $this->app = $app;
  }

  /**
   * {@inheritDoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    $current_uri = $request->getRequestUri();
    // @todo: Use the REST OAI-PMH module's repository_path config value.
    // \Drupal::config('rest_oai_pmh.settings')->get('repository_path')->get('repository_path');
    // should work (that syntax works for other configs) but it's coming back
    // NULL in all cases. For now, we hard code it to the default endpoint path.
    $oai_endpoint = '/oai/request';
    $list_records_request = $oai_endpoint . '?verb=ListRecords';
    if (strpos($current_uri, $list_records_request) === 0) {
      $ip_address = $request->getClientIp();
      // Get hostname when there is one, otherwise just
      // use harvester's IP address.
      $hostname = gethostbyaddr($ip_address);
      $hostname = $hostname ? $hostname : '';

      $rest_oai_pmh_config = \Drupal::config('islandora_repository_reports.settings')->get('islandora_repository_reports_pie_or_doughnut');
      $database = \Drupal\Core\Database\Database::getConnection();
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
