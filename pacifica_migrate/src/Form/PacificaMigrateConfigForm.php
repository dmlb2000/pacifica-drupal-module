<?php

namespace Drupal\pacifica_migrate\Form;

use Drupal\node\Entity\NodeType;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class PacificaMigrateConfigForm extends ConfigFormBase {

  /**
   * PacificaMigrateConfigForm constructor.
   * @param $config_factory
   */
  public function __construct( ConfigFactoryInterface $config_factory) {
    parent:: __construct($config_factory);
    $this->setConfigFactory($config_factory);
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['pacifica_migrate.settings'];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'pacifica_migrate_admin_settings';
  }

  public function buildForm(array $form, $form_state) {
    /** @var \Drupal\pacifica_migrate\PacificaMigrate $nodeValues */

    $config = \Drupal::config('pacifica_migrate.settings');
    $config = $config->get('pacifica_migrate');
    $db_config = $config['database_settings'];
    $d2m_config = $config['drupal_to_metadata'];
    $m2d_config = $config['metadata_to_drupal'];
    $form['pacifica_migrate'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica Migration Settings',
      '#weight' => 10,
      '#collapsed' => false,
    ];
    $form['pacifica_migrate']['database_settings'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica Metadata Service Settings',
      '#weight' => 10,
      '#collapsed' => false,
    ];
    $form['pacifica_migrate']['database_settings']['host'] = [
      '#type' => 'textfield',
      '#title' => 'Metadata Server DNS/Host',
      '#required' => true,
      '#description' => 'Pacifica Metadata Service Database DNS or Fully Qualified Hostname',
      '#default_value' => $db_config['host'],
      '#attributes' => [
        'placeholder' => 'localhost.localdomain'
      ],
    ];
    $form['pacifica_migrate']['database_settings']['port'] = [
      '#type' => 'number',
      '#title' => 'Metadata Server Port',
      '#required' => true,
      '#description' => 'Pacifica Metadata Service Database Listening Port',
      '#default_value' => $db_config['port'],
      '#attributes' => [
        'placeholder' => 5432
      ],
    ];
    $form['pacifica_migrate']['database_settings']['driver'] = [
      '#type' => 'textfield',
      '#title' => 'Drupal Database Driver',
      '#required' => true,
      '#description' => 'Drupal Database Driver Name',
      '#default_value' => $db_config['driver'],
      '#attributes' => [
        'placeholder' => 'pgsql'
      ],
    ];
    $form['pacifica_migrate']['database_settings']['username'] = [
      '#type' => 'textfield',
      '#title' => 'Database Username',
      '#required' => true,
      '#description' => 'Pacifica Metadata Service Database Username',
      '#default_value' => $db_config['username'],
      '#attributes' => [
        'placeholder' => 'pacifica'
      ],
    ];
    $form['pacifica_migrate']['database_settings']['password'] = [
      '#type' => 'password',
      '#title' => 'Database User Password',
      '#required' => true,
      '#description' => 'Pacifica Metadata Service Database User Password',
      '#default_value' => $db_config['password'],
      '#attributes' => [
        'placeholder' => 'pacifica'
      ],
    ];
    $form['pacifica_migrate']['database_settings']['database'] = [
      '#type' => 'textfield',
      '#title' => 'Database Name',
      '#required' => true,
      '#description' => 'Pacifica Metadata Service Database Name',
      '#default_value' => $db_config['database'],
      '#attributes' => [
        'placeholder' => 'pacifica_metadata'
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValue('pacifica_migrate');
    \Drupal::configFactory()->getEditable('pacifica_migrate.settings')
      ->set('pacifica_migrate', $form_values)
      ->save();

    parent::submitForm($form, $form_state);
  }
}
