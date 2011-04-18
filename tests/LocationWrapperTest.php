<?php

require_once("inc/Location.php");
require_once("inc/LocationWrapper.php");

class LocationWrapperTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
	$this->db = new Database("/tmp/test.db", new LocationDatabase());
    }


    /**
     * @expectedException NotSavedException
     */
    public function testCreateThrows()
    {
	$this->setExpectedException('NotSavedException');
	$turku = new Location("Turku");
	$turku->getId();
    }

    public function testInsertLocation()
    {
	$turku = new Location("Turku");
	$locs = new LocationWrapper($this->db);
	$id = $locs->save($turku);
	$this->assertTrue(is_numeric($id));
    }

    public function testExplicitCreateLocation()
    {
	$locs = new LocationWrapper($this->db);
	$id = $locs->createNew(new Location("Turku"));
	$locations = new LocationWrapper($this->db);
	$turku = $locations->getById($id);
	$this->assertEquals($turku->getName(), "Turku");
    }

    public function testImplicitCreateLocation()
    {
	$locs = new LocationWrapper($this->db);
	$id = $locs->save(new Location("Turku"));
	$locations = new LocationWrapper($this->db);
	$turku = $locations->getById($id);
	$this->assertEquals($turku->getName(), "Turku");
    }

    public function testImplicitUpdateLocation()
    {
	$locs = new LocationWrapper($this->db);
	$id = $locs->save(new Location("Turku"));
	$loc = $locs->getById($id);
	$loc->setName("Helsinki");
	$this->assertEquals($loc->getName(), "Helsinki");
	$locs->save($loc);
	$newloc = $locs->getById($loc->getId());
	$this->assertEquals($newloc->getName(), "Helsinki");
    }

    /**
     * @expectedException NotFoundException
     */
    public function testNotFoundException()
    {
	$locs = new LocationWrapper($this->db);
	$locs->getById(-1);
    }

    public function tearDown()
    {
	unset($this->db);
	unlink("/tmp/test.db");
    }
}
