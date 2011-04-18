<?php

abstract class Wrapper
{
    protected $db;
    private static $instance = NULL;
    public function __construct($db)
    {
	$this->db = $db;
    }

    public function save($model)
    {
	try
	{
	    return $this->update($model);
	}
	catch(NotSavedException $e) {
	    return $this->createNew($model);
	}
    }

    public abstract function createNew($model);
    public abstract function update($model);
    public abstract function getById($id);
}
