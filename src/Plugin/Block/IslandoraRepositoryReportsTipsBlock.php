<?php

/**
 * @file
 */

namespace Drupal\islandora_repository_reports\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block showing some tips on using Islandora Repository Reports output.
 *
 * @Block(
 * id = "islandora_repository_reports_tips",
 * admin_label = @Translation("Tips on using Islandora Repository Reports"),
 * category = @Translation("Islandora"),
 * )
 */
class IslandoraRepositoryReportsTipsBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = ['#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => [
        t('To download the chart as an image, right/alt click on it.'),
	t('To remove entries from the chart, click on them in the legend.'),
        ]
      ];
      return array (
        '#theme' => 'islandora_repository_reports_tips_block',
        '#content' => $content,
      );
  }
}
