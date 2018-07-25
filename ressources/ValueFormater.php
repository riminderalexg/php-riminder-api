<?php

  /**
   *
   */
  class ValueFormater
  {
    const VALID_EXT = ['pdf', 'png', 'jpg', 'jpeg', 'bmp', 'doc', 'docx', 'rtf', 'dotx', 'odt', 'odp', 'ppt', 'pptx', 'rtf', 'msg'];
    const METADATA_MANDATORY_FIELD = ['filter_reference' => 'ValueFormater::format_identForTrainingMetadata',
    'stage' => 'ValueFormater::format_nothing',
    'stage_timestamp' => 'ValueFormater::format_dateToTimestamp',
    'rating' => 'ValueFormater::format_nothing',
    'rating_timestamp' => 'ValueFormater::format_dateToTimestamp'];

    // FIXME: Find a better name
    // ident_to_string is used to get the string out of an ident when argument can
    // be embiguous ($ident is a string return it $ident is a RiminderIdent return its value)
    static function ident_to_string($ident) {
      if (is_string($ident)) {
        return $ident;
      }
      if (!empty($ident) && $ident instanceof RiminderIdent) {
        return $ident->getValue();
      }
      return null;
    }

    // Yep it doesn't do anything.
    private function format_nothing($value, $argName = 'yep', $acceptNull=False) {
      return $value;
    }

    private static function format_identForTrainingMetadata($ident, $argName = 'filter_reference', $acceptNull=False) {
      // No null allowed here.
      $identStr = self::ident_to_string($ident);
      if (empty($identStr)) {
        $mess = "A non null ".$argName." is mandatory for training metadata.";
        throw new \RiminderApiArgumentException($mess, 1);
      }
      return $identStr;
    }

    // Check if training metadas are valid and modify the fields that need formating
    public static function format_trainingMetadata(&$metadatas) {
      if (is_null($metadatas)) {
        return $metadatas;
      }
      // TODO: check if it is a list (C style)
      if (!is_array($metadatas)) {
        $mess = "Training metadatas have to be a list of object.";
        throw new \RiminderApiArgumentException($mess, 1);
      }
      foreach ($metadatas as &$metadata) {
        foreach (self::METADATA_MANDATORY_FIELD as $mandat_field => $mandat_formater) {

          if (!array_key_exists($mandat_field, $metadata)) {
            $mess = $mandat_field." is mandatory for training metadata.";
            throw new \RiminderApiArgumentException($mess, 1);
          }
          $metadata[$mandat_field] = $mandat_formater($metadata[$mandat_field], $mandat_field, True);
        }
      }
      return $metadatas;
    }

    public static function is_extensionValid(string $file) {
      $file_ext = pathinfo($file, PATHINFO_EXTENSION);

      if (in_array($file_ext, self::VALID_EXT)) {
        return true;
      }
      return false;
    }

    // Ensure that a date is a timestamp as a string or extract/transform it to
    // get the timestamp
    public static function format_dateToTimestamp($date, $argName = 'arg', $acceptNull=False) {

      if (is_null($date) && $acceptNull) {
        return $date;
      }
      if (is_int($date)){
        return strval($date);
      }
      if (is_string($date) && intval($date) != 0) {
        return $date;
      }
      if (!($date instanceof DateTime)) {
        $mess = $argName.' is not a valid date.';
        throw new \RiminderApiArgumentException($mess, 1);
      }
      return strval($date->getTimestamp());
    }

  }



?>
