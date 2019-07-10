<?php

namespace Drupal\pacifica\Metadata;

use GuzzleHttp\Client;

class PacificaMetadataAPI {

  /*
   * Metadata Host
   * @var string
   */
  public $host;

  /*
   * Metadata Port
   * @var integer
   */
  public $port;

  /*
   * Metadata Protocol
   * @var string
   */
  public $proto;

  /*
   * Metadata Path
   * @var string
   */
  public $path;

  public function __construct($parameters = array()) {
    foreach($parameters as $key => $value) {
       $this->$key = $value;
    }
  }

  public function getUrl() {
    return "{$this->proto}://{$this->host}:{$this->port}{$this->path}";
  }

  public function getObjectList() {
    $client = new Client(array(
      'base_uri' => $this->getUrl(),
    ));
    $response = $client->get("/objectinfo/list");
    $body = $response->getBody()->getContents();
    \Drupal::logger('pacifica_synch')->error("Obj List @array", array('@array' => var_export($body, true)));
    return json_decode($body, TRUE)['available_objects'];
  }
};
