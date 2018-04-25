<?php

  class RimindapiJob
  {
    public function __construct($parent) {
      $this->rimindapi = $parent;
    }

    public function getJobs() {
      $resp = $this->rimindapi->_rest->get("jobs");
      return $resp->decode_response();
    }

    public function get($job_id) {
      $resp = $this->rimindapi->_rest->get("job/$job_id");
      return $resp->decode_response();
    }
  }
 ?>
