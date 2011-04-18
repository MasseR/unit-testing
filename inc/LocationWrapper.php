<?php

require_once("inc/Wrapper.php");

class LocationWrapper extends Wrapper
{
    public function getById($id)
    {
	try
	{
	    $ret = $this->db->bind_query("SELECT id, location FROM Location WHERE id=:id", array(":id" => $id));
	    $model = new Location($ret['location']);
	    return $model->setId($ret['id']);
	} catch(NotFoundException $e) {
	    throw new NotFoundException("Location not found with id " . $id);
	}
    }


    public function createNew($model)
    {
	$name = $model->getName();
	$this->db->bind_exec("INSERT INTO Location (location) VALUES (:loc)", array(":loc" => $name));
	$ret = $this->db->bind_query("SELECT id FROM Location where location=:loc", array(":loc" => $name));
	return $ret['id'];
    }

    public function update($model)
    {
	$this->db->bind_exec("UPDATE Location SET location=:loc WHERE id=:id", array(":id" => $model->getId(), ":loc" => $model->getName()));
    }
}
