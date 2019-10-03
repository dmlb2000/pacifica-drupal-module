<?php

namespace Drupal\pacifica_migrate\Plugin\migrate\source;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Core\State\StateInterface;

/**
 * Minimalistic example for a SqlBase source plugin.
 *
 * @MigrateSource(
 *   id = "pacifica_table",
 *   source_module = "pacifica_migrate",
 * )
 */
class PacificaTable extends SqlBase {

  /**
   * The table to sync from.
   *
   * @var string
   */
  protected $pacificaTable;

  /**
   * The alias for the table.
   *
   * @var string
   */
  protected $pacificaAlias;

  /**
   * The list of field names.
   *
   * @var array
   */
  protected $pacificaFields;
  /**
   * The list of table IDs and types.
   *
   * @var array
   */
  protected $pacificaIds;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);
    $this->pacificaTable = $configuration['pacifica_table'];
    $this->pacificaAlias = $configuration['pacifica_alias'];
    $this->pacificaFields = $configuration['pacifica_fields'];
    $this->pacificaIds = $configuration['pacifica_ids'];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Source data is queried from 'curling_games' table.
    $query = $this->select($this->pacificaTable, $this->pacificaAlias)
      ->fields($this->pacificaAlias, $this->pacificaFields);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array();
    foreach($this->pacificaFields as $field) {
      $fields[$field] = $this->t($field);
    }
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return $this->pacificaIds;
      
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    foreach($this->pacificaFields as $field){
      $value = $row->getSourceProperty($field);
      try {
        $row->setSourceProperty($field, DateTimePlus::createFromFormat('Y-m-d H:i:s', $value)->getTimestamp());
      } catch (\InvalidArgumentException $exc) {
        continue;
      }
    }
    return parent::prepareRow($row);
  }
}
