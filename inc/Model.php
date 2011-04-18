<?php

abstract class Model
{
    protected $id = null;
    protected $modelname = "Model";
    public function getId()
    {
	if($this->id === NULL)
	    throw new NotSavedException($this->modelname . " is not yet saved");
	return $this->id;
    }

    public function setId($id)
    {
	$this->id = $id;
	return $this;
    }
}
