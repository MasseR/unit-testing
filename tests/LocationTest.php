<?php

require_once("inc/Location.php");

class LocationTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
	$turku = new Location("Turku");
	$this->assertEquals("Turku", $turku->getName());
	return $turku;
    }

    /**
     * @depends testCreate
     * @expectedException NotSavedException
     */
    public function testIdThrows($turku)
    {
	$turku->getId();
    }

    public function testSetId()
    {
	$turku = new Location("Turku");
	$turku->setId(1);
	$this->assertEquals(1, $turku->getId());
    }

    public function testSetName()
    {
	$turku = new Location("Turku");
	$helsinki = $turku->setName("Helsinki");
	$this->assertEquals("Helsinki", $turku->getName());
	$this->assertEquals("Helsinki", $helsinki->getName());
    }
}
