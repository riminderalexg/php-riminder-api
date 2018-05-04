<?php

  class RiminderProfile
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    private static function serializeSourceIds($source_ids) {
      $res = "";
      $i = 0;
      foreach ($source_ids as $source_id) {
        $res = $res . $source_id;
        if (++$i !== count($source_ids)) {
          $res = $res . ',';
        }
      }
      return($res);
    }

    private static function dateToTimestamp($date) {

      if (is_int($date)){
        return $date;
      }
      if ($date != new DateTime()) {
        return null;
      }
      return $date->getTimestamp();
    }

    public function getProfiles($source_ids, $date_start, $date_end, $page = 1, $limit = null, $sort_by = null, $seniority = null, $job_id = null, $stage = null) {


      $query = array (
        'source_ids'  => RiminderProfile::serializeSourceIds($source_ids),
        'date_start'  => RiminderProfile::dateToTimestamp($date_start),
        'date_end'    => RiminderProfile::dateToTimestamp($date_end),
      );
      if ($page != null) {
        $query['page'] = $page;
      }
      if ($seniority != null) {
        $query['seniority'] = $seniority;
      }
      if ($job_id != null) {
        $query['job_id'] = $job_id;
      }
      if ($stage != null) {
        $query['stage'] = $stage;
      }
      if ($limit != null) {
        $query['limit'] = $limit;
      }
      if ($sort_by != null) {
        $query['sort_by'] = $sort_by;
      }
      //var_dump($query);
      $resp = $this->riminder->_rest->get("profiles", $query);
      return $resp->decode_response();
    }

    public function add($source_id, $file, $profile_reference, $reception_date) {
      $bodyParams = array (
        'source_id'           => $source_id,
        'file'                => $file,
        'profile_reference'   => $profile_reference,
        'timestamp_reception' => RiminderProfile::dateToTimestamp($reception_date)
      );
      $resp = $this->riminder->_rest->post("profile", $bodyParams);
      return $resp->decode_response();
    }

    public function get($profile_id, $source_id) {
      $query = array(
        'source_id' => $source_id
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id", $query);
      return $resp->decode_response();
    }

    public function getDocuments($profile_id, $source_id) {
      $query = array(
        'source_id' => $source_id
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/documents", $query);
      return $resp->decode_response();
    }

    public function getExtractions($profile_id, $source_id) {
      $query = array(
        'source_id' => $source_id
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/extractions", $query);
      return $resp->decode_response();
    }

    public function getJobs($profile_id, $source_id) {
      $query = array(
        'source_id' => $source_id
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/jobs", $query);
      return $resp->decode_response();
    }

    public function updateStage($profile_id, $source_id, $job_id, $stage) {
      $bodyParams = array(
        'job_id'    => $job_id,
        'stage'     => $stage,
        'source_id' => $source_id
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/stage");
      return $resp->decode_response();
    }

    public function updateRating($profile_id, $source_id, $job_id, $rating) {
      $bodyParams = array(
        'job_id'     => $job_id,
        'stage'      => $rating,
        'source_id'  => $source_id
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/rating");
      return $resp->decode_response();
    }

  }
 ?>
