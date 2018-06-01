<?php
  require_once 'ResponseChecker.php';

  class RiminderFilter
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function getFilters() {
      $resp = $this->riminder->_rest->get("filters");
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function get($filter_id, $filter_reference) {
      $query = array(
        'filter_id'        => $filter_id,
        'filter_reference' => $filter_reference
      );
      $resp = $this->riminder->_rest->get("filter", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }
  }
 ?>
