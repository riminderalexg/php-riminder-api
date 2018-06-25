<?php

require_once __DIR__ . '/ressources/filter.php';
require_once __DIR__ . '/ressources/profile.php';
require_once __DIR__ . '/ressources/source.php';
require_once __DIR__ . '/ressources/webhook.php';
require_once __DIR__ . '/ressources/GuzzleWrapper.php';

class Riminder
{
  public $DEFAULT_HOST = "https://www.riminder.net/sf/public/api/";
  public $DEFAULT_HOST_BASE = "v1.0/";

  public function __construct($apiSecret, $webhookSecret=null) {
    $this->auth = array();
    $this->webhookSecret = $webhookSecret;

    $this->_rest = new GuzzleWrapper(array(
      "base_url"     => $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE,
      "headers"      => ["X-API-KEY"     => $apiSecret],
    ), $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE);

    $this->filter   = new RiminderFilter($this);
    $this->profile  = new RiminderProfile($this);
    $this->source   = new RiminderSource($this);
    $this->webhook  = new RiminderWebhook($this);
  }

}
?>
