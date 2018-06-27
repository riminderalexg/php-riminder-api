<?php

  require_once 'Ident.php';
  /**
   *
   */
  class RiminderFilterIdent extends RiminderIdent
  { }

  /**
   *
   */
  class FilterReference extends RiminderFilterIdent
  {

    function __construct($value)
    {
      $this->name = 'filter_reference';
      $this->value = $value;
    }
  }

  class FilterID extends RiminderFilterIdent
  {

    function __construct($value)
    {
      $this->name = 'filter_id';
      $this->value = $value;
    }
  }


 ?>
