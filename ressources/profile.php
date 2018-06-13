<?php

  require_once 'RequestBodyUtils.php';

  class RiminderProfile
  {
    const VALID_EXT = ['pdf', 'png', 'jpg', 'doc', 'docx', 'rtf', 'dotx'];
    const INVALID_FILENAME = ['.', '..'];

    public function __construct($parent) {
      $this->riminder = $parent;
    }

    private static function is_extensionValid(string $file) {
      $file_ext = pathinfo($file, PATHINFO_EXTENSION);

      if (in_array($file_ext, self::VALID_EXT)) {
        return true;
      }
      return false;
    }

    private static function join_2_path($a, $b) {
      $res = $a;
      if ($a[strlen($a) - 1] != '/' && $b[0] != '/'){
        $res = $res.'/';
      }
      $res = $res.$b;
      return $res;
    }

    private static function getFileToSend($path, $recurs) {
      $dir_paths = scandir($path);
      $res = [];

      foreach ($dir_paths as $dir_path) {
        $true_path = self::join_2_path($path, $dir_path);
        if (is_dir($true_path) && $recurs) {
            if (!in_array($dir_path, self::INVALID_FILENAME)){
              $res = array_merge($res, self::getFileToSend($true_path, $recurs));
              continue;
            }
        }
        if (self::is_extensionValid($dir_path)) {
          $res[] = $true_path;
        }
      }
      return $res;
    }

    private static function serializeSourceIds($source_ids) {
      if (!is_array($source_ids)) {
        $mess = sprintf('Source_ids should be an array.');
        throw new \RiminderApiArgumentException($mess, 1);
      }
      $res = json_encode($source_ids);
      return($res);
    }

    private static function argDateToTimestamp($date, $argName = 'arg') {

      if (is_int($date)){
        return $date;
      }
      if (!($date instanceof DateTime)) {
        $mess = $argName.' is not a valid date.';
        throw new \RiminderApiArgumentException($mess, 1);
      }
      return $date->getTimestamp();
    }

    private static function assert_querykey_exist(array $query, string $key) {
      if (!array_key_exists($key, $query)) {
        throw new \RiminderApiArgumentException($key." is absent and mandatory from query.", 1);
      }
    }

    public function getProfiles(array $query) {
      self::assert_querykey_exist($query, 'source_ids');
      self::assert_querykey_exist($query, 'date_start');
      self::assert_querykey_exist($query, 'date_end');

      $query['source_ids'] = RiminderProfile::serializeSourceIds($query['source_ids']);
      $query['date_start'] = RiminderProfile::argDateToTimestamp($query['date_start']);
      $query['date_end'] = RiminderProfile::argDateToTimestamp($query['date_end']);
      $resp = $this->riminder->_rest->get("profiles", $query);
      file_put_contents("zap", $resp->getBody());
      // var_dump($resp);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function add($source_id, $file_path, $profile_reference=null, $reception_date=null, $training_metadata=null) {
      $bodyParams = array (
        'source_id'           => $source_id
      );

      RequestBodyUtils::add_if_not_null($bodyParams, 'training_metadata', $training_metadata);
      RequestBodyUtils::add_if_not_null($bodyParams, 'profile_reference', $profile_reference);
      RequestBodyUtils::add_if_not_null($bodyParams, 'timestamp_reception', $reception_date);
      if (array_key_exists('timestamp_reception', $bodyParams)){
        $bodyParams['timestamp_reception'] =  RiminderProfile::argDateToTimestamp($reception_date, 'reception_date');
      }
      $resp = $this->riminder->_rest->postFile("profile", $bodyParams, $file_path);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function add_dir($source_id, $dir_path, $recurs=false, $reception_date=null, $training_metadata=null) {
      if (!is_dir($dir_path)) {
        throw new \RiminderApiArgumentException("'".$dir_path."' is not a directory.", 1);
      }
      $files_path = self::getFileToSend($dir_path, $recurs);
      $failed_files = [];
      $succeed_files = [];

      foreach ($files_path as $file_path) {
        try {
          $resp = self::add($source_id, $file_path, null, $reception_date, $training_metadata);
          $succeed_files[$file_path] = $resp;
        } catch (\Exception $e) {
          $failed_files[$file_path] = $e;
        }
      }
      if (!empty($failed_files)) {
        throw new \RiminderApiProfileUploadException($failed_files, $succeed_files);
      }
      return $succeed_files;
    }

    public function get($profile_id, $source_id, $profile_reference=null) {
      $query = array(
        'source_id'  => $source_id
      );
      $query = array_merge($query, RequestBodyUtils::selectIdRef('profile', $profile_id, $profile_reference));
      $resp = $this->riminder->_rest->get("profile", $query);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function getDocuments($profile_id, $source_id, $profile_reference=null) {
      $query = array(
        'source_id'  => $source_id
      );
      $query = array_merge($query, RequestBodyUtils::selectIdRef('profile', $profile_id, $profile_reference));
      $resp = $this->riminder->_rest->get("profile/documents", $query);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function getParsing($profile_id, $source_id, $profile_reference=null) {
      $query = array(
        'source_id'  => $source_id
      );
      $query = array_merge($query, RequestBodyUtils::selectIdRef('profile', $profile_id, $profile_reference));
      $resp = $this->riminder->_rest->get("profile/parsing", $query);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function getScoring($profile_id, $source_id, $profile_reference=null) {
      $query = array(
        'source_id'  => $source_id
      );
      $query = array_merge($query, RequestBodyUtils::selectIdRef('profile', $profile_id, $profile_reference));
      $resp = $this->riminder->_rest->get("profile/scoring", $query);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function updateStage($profile_id, $source_id, $filter_id, $stage,
                              $profile_reference=null, $filter_reference=null) {
      $bodyParams = array(
        'stage'       => $stage,
        'source_id'   => $source_id,
      );
      $bodyParams = array_merge($bodyParams, RequestBodyUtils::selectIdRef('profile', $profile_id, $profile_reference));
      $bodyParams = array_merge($bodyParams, RequestBodyUtils::selectIdRef('filter', $filter_id, $filter_reference));
      $resp = $this->riminder->_rest->patch("profile/stage", $bodyParams);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function updateRating($profile_id, $source_id, $filter_id, $rating,
                                $profile_reference=null, $filter_reference=null) {
      $bodyParams = array(
        'rating'       => $rating,
        'source_id'   => $source_id,
      );
      $bodyParams = array_merge($bodyParams, RequestBodyUtils::selectIdRef('profile', $profile_id, $profile_reference));
      $bodyParams = array_merge($bodyParams, RequestBodyUtils::selectIdRef('filter', $filter_id, $filter_reference));
      $resp = $this->riminder->_rest->patch("profile/rating", $bodyParams);

      return json_decode($resp->getBody(), true)['data'];
    }

  }
 ?>
