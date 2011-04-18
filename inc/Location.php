<?php

require_once("inc/Model.php");
require_once("inc/NotSavedException.php");

class Location extends Model
{
    private $name;

    public function __construct($loc)
    {
	$this->modelname = "Location";
	$this->name = $loc;
    }

    public function getName()
    {
	return $this->name;
    }


    public function setName($name)
    {
	$this->name = $name;
	return $this;
    }
}
