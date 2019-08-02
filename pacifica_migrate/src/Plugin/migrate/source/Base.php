<?php

namespace Drupal\pacifica_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Core\State\StateInterface;

/**
 * Base Source Migration from Pacifica Metadata Model
 */
abstract class Base extends SqlBase {
    /**
     * {}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
        $db_config = \Drupal::configFactory()->get('pacifica_migrate.settings')->get('pacifica_migrate');
        parent::__construct(array(
            'key' => 'pacifica',
            'target' => 'default',
            'database' => $db_config['database_settings']
        ), $plugin_id, $plugin_definition, $migration, $state);
    }
}