<?php

namespace Drupal\pacifica_migrate\Plugin\migrate\source;

/**
 * Instruments Source from Pacifica Metadata Model
 * 
 * @MigrateSource(
 *   id = "metadata_instruments",
 *   source_module = "pacifica_migrate"
 * )                               
 */
class Instruments extends Base {

    /**
     * {@inheritdoc}
     */
    public function query() {
        $query = $this->select('instruments', 'pacifica_instruments')->fields(
            'pacifica_instruments', [
                'id',
                'display_name',
                'name',
                'name_short',
        ]);
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function fields() {
        return [
            'id' => $this->t('Instrument ID'),
            'display_name' => $this->t('Instrument Display Name'),
            'name' => $this->t('Instrument Machine Name'),
            'name_short' => $this->t('Instrument Short Name')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIds() {
        return [
            'id' => [
                'type' => 'integer',
                'alias' => 'pacifica_instruments',
            ],
        ];
    }
}