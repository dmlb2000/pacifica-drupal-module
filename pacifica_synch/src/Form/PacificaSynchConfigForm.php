<?php

namespace Drupal\pacifica_synch\Form;

use Drupal\node\Entity\NodeType;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\pacifica\Metadata\PacificaMetadataAPI;

class PacificaSynchConfigForm extends ConfigFormBase {

  /**
   * PacificaSynchConfigForm constructor.
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
    return ['pacifica_synch.settings'];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'pacifica_synch_admin_settings';
  }

  public function buildForm(array $form, $form_state) {
    /** @var \Drupal\pacifica_synch\PacificaSynch $nodeValues */

    $config = \Drupal::config('pacifica_synch.settings');
    $config = $config->get('pacifica_synch');
    $meta_config = $config['metadata'];
    $amqp_config = $config['amqp'];
    $form['pacifica_synch'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica Synchronization Settings',
      '#weight' => 10,
      '#collapsed' => false,
    ];
    $form['pacifica_synch']['clear_events'] = [
      '#type' => 'submit',
      '#value' => t('Clear Events'),
      '#submit' => array([$this, 'submitButtonClearEvents']),
      '#weight' => 1,
    ];
    $form['pacifica_synch']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => 'Enable Data Synchronization',
      '#required' => true,
      '#description' => 'Enable or Disable Synchronization from Pacifica Metadata Service',
      '#default_value' => $config['enabled'],
      '#attributes' => [
        'placeholder' => False
      ],
      '#weight' => 5,
    ];
    $form['pacifica_synch']['metadata'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica Metadata Settings',
      '#weight' => 10,
      '#collapsed' => false,
    ];
    $form['pacifica_synch']['amqp'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica RabbitMQ Settings',
      '#weight' => 20,
      '#collapsed' => false,
    ];
    $form['pacifica_synch']['content_mappings'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica Metadata Content Mappings',
      '#weight' => 30,
      '#collapsed' => false,
    ];
    $form['pacifica_synch']['content_mappings']['create_mapping'] = [
      '#type' => 'submit',
      '#value' => t('Create Mapping'),
      '#submit' => array([$this, 'submitButtonCreateMapping']),
      '#weight' => 1,
    ];
    $form['pacifica_synch']['content_mappings']['mappings'] = [
      '#tree' => true,
      '#type' => 'fieldset',
      '#title' => 'Pacifica Metadata Content Mappings',
      '#weight' => 30,
      '#collapsed' => false,
    ];
    $num_field_mappings = $form_state->get('num_field_mappings');
    if (empty($num_field_mappings)) {
      $num_field_mappings = array();
    }
    $meta_obj = new PacificaMetadataAPI($meta_config);
    $meta_obj_list = $meta_obj->getObjectList();
    $all_content_types = NodeType::loadMultiple();
    $content_types_list = array();
    foreach ($all_content_types as $machine_name => $content_type) {
      $content_types_list[$machine_name] = $content_type->label();
    }
    foreach($num_field_mappings as $index => $num_fields) {
      $obj_config = $config['mappings'][$index];
      $form['pacifica_synch']['content_mappings']['mappings'][$index] = [
        '#tree' => true,
        '#type' => 'fieldset',
        '#title' => 'Pacifica Metadata Content Mapping',
        '#weight' => 30,
        '#collapsed' => false,
      ];
      $form['pacifica_synch']['content_mappings']['mappings'][$index]['object_type'] = [
        '#type' => 'select',
        '#title' => 'Metadata Object Type',
        '#required' => true,
        '#description' => 'Metadata Object Type',
        '#default_value' => $obj_config['object_type'],
        '#options' => $meta_obj_list,
      ];
      $form['pacifica_synch']['content_mappings']['mappings'][$index]['content_type'] = [
        '#type' => 'select',
        '#title' => 'Content Type',
        '#required' => true,
        '#description' => 'Content Type to Save To',
        '#default_value' => $obj_config['content_type'],
        '#options' => $content_types_list,
      ];
    }
    $form['pacifica_synch']['metadata']['host'] = [
      '#type' => 'textfield',
      '#title' => 'Metadata Server DNS/Host',
      '#required' => true,
      '#description' => 'Metadata Server DNS or Fully Qualified Hostname',
      '#default_value' => $meta_config['host'],
      '#attributes' => [
        'placeholder' => 'localhost.localdomain'
      ],
    ];
    $form['pacifica_synch']['metadata']['port'] = [
      '#type' => 'number',
      '#title' => 'Metadata Server Port',
      '#required' => true,
      '#description' => 'Metadata Server Remote Listening Port',
      '#default_value' => $meta_config['port'],
      '#attributes' => [
        'placeholder' => 8121
      ],
    ];
    $form['pacifica_synch']['metadata']['proto'] = [
      '#type' => 'textfield',
      '#title' => 'Metadata Server Protocol',
      '#required' => true,
      '#description' => 'Metadata Server URL Protocol',
      '#default_value' => $meta_config['proto'],
      '#attributes' => [
        'placeholder' => 'http'
      ],
    ];
    $form['pacifica_synch']['metadata']['path'] = [
      '#type' => 'textfield',
      '#title' => 'Metadata Server Path',
      '#required' => true,
      '#description' => 'Metadata Server URL Path',
      '#default_value' => $meta_config['path'],
      '#attributes' => [
        'placeholder' => '/'
      ],
    ];
    $form['pacifica_synch']['amqp']['host'] = [
      '#type' => 'textfield',
      '#title' => 'RabbitMQ DNS/Hostname',
      '#required' => true,
      '#description' => 'RabbitMQ DNS or Hostname',
      '#default_value' => $amqp_config['host'],
      '#attributes' => [
        'placeholder' => 'localhost.localdomain'
      ],
    ];
    $form['pacifica_synch']['amqp']['port'] = [
      '#type' => 'number',
      '#title' => 'RabbitMQ Port',
      '#required' => true,
      '#description' => 'RabbitMQ Remote Port',
      '#default_value' => $amqp_config['port'],
      '#attributes' => [
        'placeholder' => 5672
      ],
    ];
    $form['pacifica_synch']['amqp']['user'] = [
      '#type' => 'textfield',
      '#title' => 'RabbitMQ Username',
      '#required' => true,
      '#description' => 'RabbitMQ Authentication Username',
      '#default_value' => $amqp_config['user'],
      '#attributes' => [
        'placeholder' => 'guest'
      ],
    ];
    $form['pacifica_synch']['amqp']['pass'] = [
      '#type' => 'textfield',
      '#title' => 'RabbitMQ Password',
      '#required' => true,
      '#description' => 'RabbitMQ Authentication Password',
      '#default_value' => $amqp_config['pass'],
      '#attributes' => [
        'placeholder' => 'guest'
      ],
    ];
    $form['pacifica_synch']['amqp']['vhost'] = [
      '#type' => 'textfield',
      '#title' => 'RabbitMQ vHost',
      '#required' => true,
      '#description' => 'RabbitMQ Remote vHost',
      '#default_value' => $amqp_config['vhost'],
      '#attributes' => [
        'placeholder' => '/'
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!in_array($form_state->getValue(array('pacifica_synch', 'metadata', 'proto')), array('http', 'https'))) {
      $form_state->setErrorByName(
        array('pacifica_synch', 'metadata', 'proto'),
        $this->t('Protocol should be one of http or https.')
      );
    }
    parent::validateForm($form, $form_state);
  }

  public function submitButtonCreateMapping(array &$form, FormStateInterface $form_state) {
    $num_field_mappings = $form_state->get('num_field_mappings');
    if (empty($num_field_mappings)) {
      $num_field_mappings = array();
    }
    array_push($num_field_mappings, 0);
    $form_state->set('num_field_mappings', $num_field_mappings);
    $form_state->setRebuild(TRUE);
    \Drupal::logger('pacifica_synch')->error("New Array @array", array('@array' => var_export($num_field_mappings, true)));
  }

  public function submitButtonClearEvents(array &$form, FormStateInterface $form_state) {
    \Drupal::database()->truncate('pacifica_synch_events')->execute();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValue('pacifica_synch');
    \Drupal::configFactory()->getEditable('pacifica_synch.settings')
      ->set('pacifica_synch', $form_values)
      ->save();

    parent::submitForm($form, $form_state);
  }
}
