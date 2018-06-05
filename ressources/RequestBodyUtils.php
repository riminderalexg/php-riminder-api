<?php
  /**
   *
   */
  require_once 'RiminderApiException.php';

  class RequestBodyUtils
  {
      public static function selectIdRef(string $field, $id=null, $reference=null, $empty_ok=false) {
        $res = array();
        if (!is_null($id)){
          $res[$field.'_id'] = $id;
        }
        if (!is_null($reference)){
          $res[$field.'_reference'] = $reference;
        }
        if (empty($res) && !$empty_ok) {
          $mess = sprintf(
            'At least one among %s_id and %s_reference have to be defined',
            $field, $field);
          throw new \RiminderApiArgumentException($mess, 1);
        }
        return $res;
      }

      public static function add_if_not_null(array &$to_fill, $key, $elem) {
        if ($elem != null) {
          $to_fill[$key] = $elem;
        }
      }
  }

?>
