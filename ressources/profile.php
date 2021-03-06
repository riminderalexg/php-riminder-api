<?php

  require_once 'RequestBodyUtils.php';
  require_once 'ValueFormater.php';

  class RiminderProfile
  {
    const INVALID_FILENAME = ['.', '..'];

    public function __construct($parent) {
      $this->riminder = $parent;
      $this->rating = new RiminderProfileRating($parent);
      $this->stage = new RiminderProfileStage($parent);
      $this->scoring = new RiminderProfileScoring($parent);
      $this->parsing = new RiminderProfileParsing($parent);
      $this->document = new RiminderProfileDocument($parent);
      $this->json = new RiminderProfileJson($parent);
    }

    private static function join_2_path($a, $b) {
      $res = $a;
      if ($a[strlen($a) - 1] != '/' && $b[0] != '/'){
        $res = $res.'/';
      }
      $res = $res.$b;
      return $res;
    }

    // GetFileToSend scan all file in directory and get potential
    // resume paths and return them
    private static function getFileToSend($path, $recurs) {
      $dir_paths = scandir($path);
      $res = [];

      foreach ($dir_paths as $dir_path) {
        $true_path = self::join_2_path($path, $dir_path);

        // when $recurs is true check in subdir
        if (is_dir($true_path) && $recurs) {
            if (!in_array($dir_path, self::INVALID_FILENAME)){
              $res = array_merge($res, self::getFileToSend($true_path, $recurs));
              continue;
            }
        }
        if (ValueFormater::is_extensionValid($dir_path)) {
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

    // Check if key is present in query or throw an error
    private static function assert_querykey_exist(array $query, string $key) {
      if (!array_key_exists($key, $query)) {
        throw new \RiminderApiArgumentException($key." is absent and mandatory from query.", 1);
      }
    }

    public function list(array $query) {
      self::assert_querykey_exist($query, 'source_ids');
      self::assert_querykey_exist($query, 'date_start');
      self::assert_querykey_exist($query, 'date_end');

      $query['source_ids'] = RiminderProfile::serializeSourceIds($query['source_ids']);
      $query['date_start'] = ValueFormater::format_dateToTimestamp($query['date_start']);
      $query['date_end'] = ValueFormater::format_dateToTimestamp($query['date_end']);

      $resp = $this->riminder->_rest->get("profiles", $query);
      return json_decode($resp->getBody(), true)['data'];
    }

    public function add(string $source_id, string $file_path, $profile_reference=null, $reception_date=null, $training_metadata=null) {

      // ensure that profile reference is a string
      // cause it can be a ProfileReference object or a string
      $profile_reference = ValueFormater::ident_to_string($profile_reference);
      $trainingMetadata = ValueFormater::format_trainingMetadata($training_metadata);

      $bodyParams = array (
        'source_id'           => $source_id
      );
      RequestBodyUtils::add_if_not_null($bodyParams, 'training_metadata', $training_metadata);
      RequestBodyUtils::add_if_not_null($bodyParams, 'profile_reference', $profile_reference);
      RequestBodyUtils::add_if_not_null($bodyParams, 'timestamp_reception', $reception_date);
      if (array_key_exists('timestamp_reception', $bodyParams)){
        $bodyParams['timestamp_reception'] =  ValueFormater::format_dateToTimestamp($reception_date, 'reception_date');
      }

      $resp = $this->riminder->_rest->postFile("profile", $bodyParams, $file_path);
      return json_decode($resp->getBody(), true)['data'];
    }

    public function addlist(string $source_id, string $dir_path, bool $recurs=false, $reception_date=null, $training_metadata=null) {
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
      // If at least a file failed there is an exp,
      // the exp contains list of suceed file too
      if (!empty($failed_files)) {
        throw new \RiminderApiProfileUploadException($failed_files, $succeed_files);
      }
      return $succeed_files;
    }

    public function get(RiminderProfileIdent $profile_ident, string $source_id) {
      $query = array(
        'source_id'  => $source_id
      );
      $profile_ident->addToArray($query);
      $resp = $this->riminder->_rest->get("profile", $query);

      return json_decode($resp->getBody(), true)['data'];
    }
  }

  /**
   *
   */
  class RiminderProfileDocument
  {

    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function list(RiminderProfileIdent $profile_ident, string $source_id) {
      $query = array(
        'source_id'  => $source_id
      );
      $profile_ident->addToArray($query);
      $resp = $this->riminder->_rest->get("profile/documents", $query);

      return json_decode($resp->getBody(), true)['data'];
    }
  }

  /**
   *
   */
  class RiminderProfileParsing
  {

    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function get(RiminderProfileIdent $profile_ident, string $source_id) {
      $query = array(
        'source_id'  => $source_id
      );
      $profile_ident->addToArray($query);
      $resp = $this->riminder->_rest->get("profile/parsing", $query);

      return json_decode($resp->getBody(), true)['data'];
    }
  }

/**
 *
 */
  class RiminderProfileScoring
  {

    public function __construct($parent) {
      $this->riminder = $parent;
    }


    public function list(RiminderProfileIdent $profile_ident, string $source_id) {
      $query = array(
        'source_id'  => $source_id
      );
      $profile_ident->addToArray($query);
      $resp = $this->riminder->_rest->get("profile/scoring", $query);

      return json_decode($resp->getBody(), true)['data'];
    }
  }

  /**
   *
   */
  class RiminderProfileStage
  {

    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function set(RiminderProfileIdent $profile_ident, string $source_id, RiminderFilterIdent $filter_ident, string $stage) {
      $bodyParams = array(
        'stage'       => $stage,
        'source_id'   => $source_id,
      );
      $profile_ident->addToArray($bodyParams);
      $filter_ident->addToArray($bodyParams);
      $resp = $this->riminder->_rest->patch("profile/stage", $bodyParams);

      return json_decode($resp->getBody(), true)['data'];
    }
  }

  /**
   *
   */
  class RiminderProfileRating
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function set(RiminderProfileIdent $profile_ident, string $source_id, RiminderFilterIdent $filter_ident, int $rating) {
      $bodyParams = array(
        'rating'       => $rating,
        'source_id'   => $source_id,
      );
      $profile_ident->addToArray($bodyParams);
      $filter_ident->addToArray($bodyParams);
      $resp = $this->riminder->_rest->patch("profile/rating", $bodyParams);

      return json_decode($resp->getBody(), true)['data'];
    }

  }

  class RiminderProfileJson
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function check(array $profileData, array $trainingMetadata=[]) {

      $trainingMetadata = ValueFormater::format_trainingMetadata($trainingMetadata);
      $bodyParams = array(
        'profile_json'       => $profileData,
        'training_metadata'  => $trainingMetadata
      );
      $resp = $this->riminder->_rest->post("profile/json/check", $bodyParams);

      return json_decode($resp->getBody(), true)['data'];
    }

    public function add(string $source_id, array $profileData, array $trainingMetadata=[], $profile_reference=null, $timestamp_reception=null) {

      $trainingMetadata = ValueFormater::format_trainingMetadata($trainingMetadata);
      if (!empty($profile_reference) && $profile_reference instanceof ProfileReference) {
        $profile_reference = $profile_reference->getValue();
      }
      $timestamp_reception = ValueFormater::format_dateToTimestamp($timestamp_reception, 'timestamp_reception');

      $bodyParams = array(
        'source_id'           => $source_id,
        'profile_json'        => $profileData,
        'training_metadata'   => $trainingMetadata
      );

      RequestBodyUtils::add_if_not_null($bodyParams, 'profile_reference', $profile_reference);
      RequestBodyUtils::add_if_not_null($bodyParams, 'timestamp_reception', $timestamp_reception);
      $resp = $this->riminder->_rest->post("profile/json", $bodyParams);

      return json_decode($resp->getBody(), true)['data'];
    }
}
 ?>
