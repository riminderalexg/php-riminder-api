<?php
  require_once 'ResponseChecker.php';

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

    private static function argDateToTimestamp($date, $argName = 'arg') {

      if (is_int($date)){
        return $date;
      }
      if (!($date instanceof DateTime)) {
        throw new \RiminderApiArgumentException('date', $argName, 1);
      }
      return $date->getTimestamp();
    }

    public function getProfiles(array $source_ids, $date_start, $date_end, int $page = 1, int $limit = null, $sort_by = null, $seniority = null, $filter_id = null, $stage = null) {

      $query = array (
        'source_ids'  => RiminderProfile::serializeSourceIds($source_ids),
        'date_start'  => RiminderProfile::argDateToTimestamp($date_start, 'date_start'),
        'date_end'    => RiminderProfile::argDateToTimestamp($date_end, 'date_end'),
      );
      if ($page != null) {
        $query['page'] = $page;
      }
      if ($seniority != null) {
        $query['seniority'] = $seniority;
      }
      if ($filter_id != null) {
        $query['filter_id'] = $filter_id;
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
      $resp = $this->riminder->_rest->get("profiles", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function add($source_id, $file, $profile_reference, $reception_date) {
      $bodyParams = array (
        'source_id'           => $source_id,
        'file'                => $file,
        'profile_reference'   => $profile_reference,
        'timestamp_reception' => RiminderProfile::argDateToTimestamp($reception_date, 'reception_date')
      );
      $resp = $this->riminder->_rest->post("profile", $bodyParams);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function get($profile_id, $source_id) {
      $query = array(
        'source_id'  => $source_id,
        'profile_id' => $profile_id
      );
      $resp = $this->riminder->_rest->get("profile", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function getDocuments($profile_id, $source_id) {
      $query = array(
        'source_id'  => $source_id,
        'profile_id' => $profile_id
      );
      $resp = $this->riminder->_rest->get("profile/documents", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function getParsing($profile_id, $source_id) {
      $query = array(
        'source_id'  => $source_id,
        'profile_id' => $profile_id
      );
      $resp = $this->riminder->_rest->get("profile/parsing", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function getScoring($profile_id, $source_id) {
      $query = array(
        'source_id'  => $source_id,
        'profile_id' => $profile_id
      );
      $resp = $this->riminder->_rest->get("profile/scoring", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function updateStage($profile_id, $source_id, $job_id, $stage) {
      $bodyParams = array(
        'job_id'      => $job_id,
        'stage'       => $stage,
        'source_id'   => $source_id,
        'profile_id'  => $profile_id
      );
      $resp = $this->riminder->_rest->patch("profile/stage");
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

    public function updateRating($profile_id, $source_id, $job_id, $rating) {
      $bodyParams = array(
        'job_id'      => $job_id,
        'stage'       => $rating,
        'source_id'   => $source_id,
        'profile_id'  => $profile_id
      );
      $resp = $this->riminder->_rest->patch("profile/rating");
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }

  }
 ?>
