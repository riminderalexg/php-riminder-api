<?php

  class RiminderProfile
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function serializeSourceIds($source_ids) {
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
    public function getProfiles($source_ids, $date_start, $date_end, $page = 1, $limit = null, $sort_by = null, $seniority = null, $job_id = null, $stage = null) {


      $query = array (
        'source_ids'  => $this->serializeSourceIds($source_ids),
        'date_start'  => $date_start,
        'date_end'    => $date_end,
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

    public function add($source_id, $file, $profile_reference, $timestamp_reception) {
      $bodyParams = array (
        'source_id'           => $source_id,
        'file'                => $file,
        'profile_reference'   => $profile_reference,
        'timestamp_reception' => $timestamp_reception
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

    public function updateStage($profile_id, $job_id, $stage) {
      $bodyParams = array(
        'job_id' => $job_id,
        'stage'  => $stage
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/stage");
      return $resp->decode_response();
    }

    public function updateRating($profile_id, $job_id, $rating) {
      $bodyParams = array(
        'job_id' => $job_id,
        'stage'  => $rating
      );
      $resp = $this->riminder->_rest->get("profile/$profile_id/rating");
      return $resp->decode_response();
    }

  }
 ?>
