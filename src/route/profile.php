<?php

  namespace riminder\rimindapi\route;

  class RimindapiProfile
  {
    public function _construct($parent) {
      $this->rimindapi = $parent;
    }

    public function getProfiles($source_ids, $date_start, $date_end, $page, $limit = null, $sort_by = null, $seniority = null, $job_id = null, $stage = null) {
      $query = array (
        'source_ids'  => $source_ids,
        'seniority'   => $seniority,
        'job_id'      => $job_id,
        'stage'       => $stage,
        'date_start'  => $date_start,
        'date_end'    => $date_end,
        'page'        => $page,
        'limit'       => $limit,
        'sort_by'     => $sort_by
      );
      $resp = $this->rimindapi->_rest->get("profiles", $query);
      return $resp->decode_response();
    }

    public function add($source_id, $file, $profile_reference, $timestamp_reception) {
      $bodyParams = array (
        'source_id'           => $source_id,
        'file'                => $file,
        'profile_reference'   => $profile_reference,
        'timestamp_reception' => $timestamp_reception
      );
      $resp = $this->rimindapi->_rest->post("profile", $bodyParams);
      return $resp->decode_response();
    }

    public function get($profile_id) {

      $resp = $this->rimindapi->_rest->get("profile/$profile_id");
      return $resp->decode_response();
    }

    public function getDocuments($profile_id) {

      $resp = $this->rimindapi->_rest->get("profile/$profile_id/documents");
      return $resp->decode_response();
    }

    public function getExtractions($profile_id) {
      $resp = $this->rimindapi->_rest->get("profile/$profile_id/extractions");
      return $resp->decode_response();
    }

    public function getJobs($profile_id) {
      $resp = $this->rimindapi->_rest->get("profile/$profile_id/jobs");
      return $resp->decode_response();
    }

    public function updateStage($profile_id, $job_id, $stage) {
      $bodyParams = array(
        'job_id' => $job_id,
        'stage'  => $stage
      );
      $resp = $this->rimindapi->_rest->get("profile/$profile_id/stage");
      return $resp->decode_response();
    }

    public function updateRating($profile_id, $job_id, $rating) {
      $bodyParams = array(
        'job_id' => $job_id,
        'stage'  => $rating
      );
      $resp = $this->rimindapi->_rest->get("profile/$profile_id/rating");
      return $resp->decode_response();
    }

  }
 ?>
