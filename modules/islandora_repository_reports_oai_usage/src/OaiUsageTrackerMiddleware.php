<?php

/**
 * @file 
 *   Contains Drupal\islandora_repository_reports_oai_usage$\OaiUsageTrackerMiddleware.
 */

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
    // @todo: Should be the REST OAI-PMH's modules repository_path config value.
    if (strpos($current_uri, '/oai/request') !== FALSE) {
      $ip_address = $request->getClientIp();
      $hostname = gethostbyaddr($ip_address);
      $hostname = $hostname ? $hostname : '';

      $database = \Drupal\Core\Database\Database::getConnection();
      $result = $database->insert('islandora_repository_reports_oai_usage_requests')
        ->fields([
          'ip_address' => $ip_address,
          'hostname' => $hostname, 
          'created' => \Drupal::time()->getRequestTime(),
          'request' => $request->getRequestUri()
	])
	->execute();
    }
    return $this->app->handle($request, $type, $catch);
  }

}
