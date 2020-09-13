<?php

namespace Drupal\islandora_repository_reports\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\NodeType;

/**
 * Controller.
 */
class IslandoraRepositoryReportsFieldListAutocompleteController extends ControllerBase {

  /**
   * Populates an autocomplete form element with field names.
   *
   * @return string
   *   Markup used by the chart.
   */
  public function main(Request $request) {
    $query_string = $request->getQueryString();
    if (!$query_string) {
      return new JsonResponse([]);
    }
    parse_str($query_string, $query_array);

    $content_types = NodeType::loadMultiple();
    $field_autocomplete_data = [];
    $seen_field_names = [];
    foreach (array_keys($content_types) as $content_type) {
      $all_field_defs = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $content_type);
      // For now, only support specific field types.
      $allowed_field_types = ['string', 'string_long', 'text', 'text_long'];
      foreach ($all_field_defs as $field_name => $field_def) {
        if (in_array($field_name, $seen_field_names)) {
          continue;
        }
        $label = $all_field_defs[$field_name]->getLabel();
        $type = $all_field_defs[$field_name]->getType();
        if (in_array($type, $allowed_field_types)) {
          if (preg_match('/' . $query_array['q'] . '/i', $label)) {
            $field_autocomplete_data[] = ['label' => $label, 'value' => $field_name];
          }
        }
        $seen_field_names[] = $field_name;
      }
    }

    return new JsonResponse($field_autocomplete_data);
  }

}
