<?php

namespace Drupal\islandora_repository_reports\Plugin\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin settings form.
 */
class IslandoraRepositoryReportsReportSelectorForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'islandora_repository_reports_report_selector';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if ($tempstore = \Drupal::service('user.private_tempstore')->get('islandora_repository_reports')) {
      $report_type = $tempstore->get('islandora_repository_reports_report_type');
    }
    else {
      $report_type = 'mimetype';
    }

    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $services = $utilities->getServices();
    natsort($services);

    $form['islandora_repository_reports_report_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Report type'),
      '#default_value' => $report_type,
      '#options' => $services,
      '#attributes' => [
        'name' => 'islandora_repository_reports_report_type',
        'id' => 'islandora_repository_reports_report_type',
      ],
    ];
    $form['islandora_repository_reports_generate_csv'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generate a CSV file of this data'),
      '#attributes' => [
        'name' => 'islandora_repository_reports_generate_csv',
      ],
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Go'),
      '#suffix' => '<span class="islandora-repository-reports-is-loading-message" style="text-align:center; display:none;">' . $this->t("Please stand by while your report is being prepared.") . '</span>',
      '#attributes' => [
        'id' => 'islandora_repository_reports_go_button',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tempstore = \Drupal::service('user.private_tempstore')->get('islandora_repository_reports');
    $tempstore->set('islandora_repository_reports_report_type', $form_state->getValue('islandora_repository_reports_report_type'));
    $tempstore->set('islandora_repository_reports_generate_csv', $form_state->getValue('islandora_repository_reports_generate_csv'));
    // Pass the entire form state in so third-party modules that alter the
    // form can retrieve their custom form values.
    $tempstore->set('islandora_repository_reports_report_form_values', $form_state);
  }

}
