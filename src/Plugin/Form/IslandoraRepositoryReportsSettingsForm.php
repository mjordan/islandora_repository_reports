<?php

namespace Drupal\islandora_repository_reports\Plugin\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin settings form.
 */
class IslandoraRepositoryReportsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'islandora_repository_reports_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'islandora_repository_reports.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('islandora_repository_reports.settings');
    $form['islandora_repository_reports_cache_report_data'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Cache report data.'),
      '#default_value' => $config->get('islandora_repository_reports_cache_report_data'),
      '#description' => $this->t('Generating data to populate charts can take a long time. Check this if you want to cache the data, or if you pregenerate your data using Drush.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('islandora_repository_reports.settings')
      ->set('islandora_repository_reports_cache_report_data', $form_state->getValue('islandora_repository_reports_cache_report_data'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
