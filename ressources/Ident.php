<?php

  /**
   *
   */
  class RiminderIdent
  {
    public $name;
    public $value;

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
