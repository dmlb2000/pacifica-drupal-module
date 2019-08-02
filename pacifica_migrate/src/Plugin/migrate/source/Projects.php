<?php

namespace Drupal\pacifica_migrate\Plugin\migrate\source;

/**
 * Projects Source from Pacifica Metadata Model
 * 
 * @MigrateSource(
 *   id = "metadata_projects",
 *   source_module = "pacifica_migrate"
 * )     
 */
class Projects extends Base {

    /**
     * {@inheritdoc}
     */
    public function query() {
        $query = $this->select('projects', 'pacifica_projects')->fields(
            'pacifica_projects', [
                'id',
                'title',
                'short_name',
                'abstract',
        ]);
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function fields() {
        return [
            'id' => $this->t('Project ID'),
            'title' => $this->t('Project Title Name'),
            'short_name' => $this->t('Project Short Name'),
            'abstract' => $this->t('Project Abstract')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIds() {
        return [
            'id' => [
                'type' => 'string',
                'alias' => 'pacifica_projects',
            ],
        ];
    }
}