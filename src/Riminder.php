<?php

require __DIR__ . '/route/job.php';
require __DIR__ . '/route/profile.php';
require __DIR__ . '/route/source.php';

class Riminder
{
  public $DEFAULT_HOST = "https://www.riminder.net/sf/public/api/";
  public $DEFAULT_HOST_BASE = "v1.0/";

  public function __construct($apiSecret) {
    $this->auth = array();

    $this->_rest = new RestClient(array(
      "base_url"     => $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE,
      "headers"      => ["Content-Type" => "application/json",
                        "X-API-KEY"     => $apiSecret],
      "content_type" => "application/json"
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
