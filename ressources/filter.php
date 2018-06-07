<?php
  
  require_once 'RequestBodyUtils.php';

  class RiminderFilter
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function getFilters() {
      $resp = $this->riminder->_rest->get("filters");
      
      return json_decode($resp->getBody(), true)['data'];
    }

    public function get($filter_id, $filter_reference=null) {
      $query = RequestBodyUtils::selectIdRef('filter', $filter_id, $filter_reference);
      $resp = $this->riminder->_rest->get("filter", $query);
      
      return json_decode($resp->getBody(), true)['data'];
    }
  }
 ?>
