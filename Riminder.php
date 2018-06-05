<?php

require __DIR__ . '/ressources/filter.php';
require __DIR__ . '/ressources/profile.php';
require __DIR__ . '/ressources/source.php';
require_once __DIR__ . '/ressources/RiminderConstant.php';

class Riminder
{
  public $DEFAULT_HOST = "https://www.riminder.net/sf/public/api/";
  public $DEFAULT_HOST_BASE = "v1.0/";

  public $Stage;
  public $Sort_by;
  public $Seniority;
  public $Order_by;
  public $Fields;
  public $Training_metadata;

  public function __construct($apiSecret) {
    $this->auth = array();

    $this->_rest = new RestClient(array(
      "base_url"     => $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE,
      "headers"      => ["X-API-KEY"     => $apiSecret],
    ));

    $this->_rest->register_decoder("json", function($data) {
      return json_decode($data, TRUE);
    });

    $this->filter   = new RiminderFilter($this);
    $this->profile  = new RiminderProfile($this);
    $this->source   = new RiminderSource($this);

    $this->Stage             = new RiminderStage();
    $this->Sort_by           = new RiminderSort_by();
    $this->Seniority         = new RiminderSeniority();
    $this->Order_by          = new RiminderOrder_by();
    $this->Fields            = new RiminderField();
    $this->Training_metadata = new RiminderTrainingMetaData();
  }

  public function setRestHost($host) {
    $this->_rest->set_option('base_url', $host);
  }

}
?>
