<?php

  /**
   *  RiminderIdent is the base class behind *Reference and *ID classes
   *  It permit to manage id and reference seamlessly.
   */
  class RiminderIdent
  {
    public $name;
    public $value;

    // Add a RiminderIdent to an array (key => value)
    public function addToArray(&$to_fill) {
      $to_fill[$this->name] = $this->value;
      return $to_fill;
    }

    public function getName() {
      return $this->name;
    }

    public function getValue() {
      return $this->value;
    }

    public function setValue(string $value) {
      $this->value = $value;
    }
  }


 ?>
