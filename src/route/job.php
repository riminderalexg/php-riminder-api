<?php

  class RiminderJob
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function getJobs() {
      $resp = $this->riminder->_rest->get("jobs");
      return $resp->decode_response();
    }

    public function get($job_id) {
      $resp = $this->riminder->_rest->get("job/$job_id");
      return $resp->decode_response();
    }
  }
 ?>
