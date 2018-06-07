<?php

require_once __DIR__ . '/ressources/filter.php';
require_once __DIR__ . '/ressources/profile.php';
require_once __DIR__ . '/ressources/source.php';
require_once __DIR__ . '/ressources/RiminderConstant.php';
require_once __DIR__ . '/ressources/GuzzleWrapper.php';

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

    $this->_rest = new GuzzleWrapper(array(
      "base_url"     => $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE,
      "headers"      => ["X-API-KEY"     => $apiSecret],
    ), $this->DEFAULT_HOST . $this->DEFAULT_HOST_BASE);

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

}
?>
