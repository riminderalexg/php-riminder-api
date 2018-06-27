<?php

  require_once 'Ident.php';
  /**
   *
   */
  class RiminderProfileIdent extends RiminderIdent
  { }

  /**
   *
   */
  class ProfileReference extends RiminderProfileIdent
  {

    function __construct($value)
    {
      $this->name = 'profile_reference';
      $this->value = $value;
    }
  }

  class ProfileID extends RiminderProfileIdent
  {

    function __construct($value)
    {
      $this->name = 'profile_id';
      $this->value = $value;
    }
  }


 ?>
