<?php
  /**
   *
   */
  require_once 'RiminderApiException.php';

  class IdReferenceSelector
  {
      public static function select(string $field, $id=null, $reference=null, $empty_ok=false) {
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
  }

?>
