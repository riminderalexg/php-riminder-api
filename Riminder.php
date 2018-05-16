<?php

require __DIR__ . '/ressources/job.php';
require __DIR__ . '/ressources/profile.php';
require __DIR__ . '/ressources/source.php';

class Riminder
{
  public $DEFAULT_HOST = "https://www.riminder.net/sf/public/api/";
  public $DEFAULT_HOST_BASE = "v1.0/";
  
  public function __construct($apiSecret) {
    $this->auth = array();

    $this->_rest = new RestClient(array(
      "base_url"     => $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE,
      "headers"      => [
                        "X-API-KEY"     => $apiSecret],
    ));

    $this->_rest->register_decoder("json", function($data) {
      return json_decode($data, TRUE);
    });

    $this->job      = new RiminderJob($this);
    $this->profile  = new RiminderProfile($this);
    $this->source   = new RiminderSource($this);
  }

  public function setRestHost($host) {
    $this->_rest->set_option('base_url', $host);
  }
}
?>
