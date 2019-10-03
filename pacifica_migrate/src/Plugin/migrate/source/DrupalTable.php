<?php

namespace Drupal\pacifica_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Core\State\StateInterface;

/**
 * Minimalistic example for a SqlBase source plugin.
 *
 * @MigrateSource(
 *   id = "drupal_table",
 *   source_module = "pacifica_migrate",
 * )
 */
class DrupalTable extends SqlBase {

  /**
   * Node type from the node table.
   *
   * @var string
   */
  protected $nodeType;

  /**
   * The list of field names.
   *
   * @var array
   */
  protected $nodeFields;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);
    $this->nodeType = $configuration['node_type'];
    $this->nodeFields = $configuration['node_fields'];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Source data is queried from 'curling_games' table.
    $query = $this->select('node', 'node')
      ->condition('node.type', $this->nodeType, '=')
      ->fields('node', ['nid', 'uuid']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('nid'),
      'uuid' => $this->t('uuid')
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'node'
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($row->getSourceProperty('nid'));
    foreach($this->nodeFields as $field) {
      if(count($node->get($field)->getValue()) > 0) {
        print_r($field);
        var_dump($node->get($field)->getValue()[0]);
        $row->setSourceProperty($field, $node->get($field)->getValue()[0]['value']);
      }
    }
    return parent::prepareRow($row);
  }
}
