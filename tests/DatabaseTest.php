<?php

require_once("inc/Database.php");

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    private $db;
    public function setUp()
    {
	$this->db = new Database("/tmp/test.db", new LocationDatabase());
    }

    public function testGetPath()
    {
	$this->assertEquals("/tmp/test.db", $this->db->getPath());
    }

    public function testSingleton()
    {
	$db = db();
	$dbnew = db();
	$this->assertEquals("/tmp/real.db", $db->getPath());
	$this->assertSame($db, $dbnew);
    }

    public function testBoundQuerySimple()
    {
	$msg = "Hello world";
	$bind = array(":msg" => $msg);
	$ret = $this->db->bind_query("SELECT :msg AS test", $bind);
	$this->assertEquals($msg, $ret['test']);
    }

    /**
     * @expectedException NotFoundException
     */
    public function testBoundQueryNotFound()
    {
	$bind = array(":msg" => "foo", ":cond" => "bar");
	$ret = $this->db->bind_query("SELECT :msg AS test WHERE test=:cond", $bind);
    }

    public function testBoundQueryMulti()
    {
	$bind = array(
	    array(":msg" => "foo"),
	    array(":msg" => "hello")
	);
	$ret = $this->db->bind_query("SELECT :msg AS test", $bind);
	$this->assertEquals(2, count($ret));
	$fst = $ret[0];
	$snd = $ret[1];
	$this->assertEquals("foo", $fst['test']);
	$this->assertEquals("hello", $snd['test']);
    }

    public function testQuery()
    {
	$ret = $this->db->query("SELECT 'Hello' as test");
	$this->assertEquals("Hello", $ret['test']);
    }

    public function testBoundExec()
    {
	$bind = array(":val" => "foo");
	$this->db->exec("CREATE TABLE test (x, UNIQUE(x))");
	$this->db->bind_exec("INSERT INTO test values (:val)", $bind);
	$x = $this->db->query("SELECT x FROM test");
	$count = $this->db->query("SELECT count(x) FROM test");
	$this->assertEquals(1, $count['count(x)']);
	$this->assertEquals("foo", $x['x']);
    }

    public function tearDown()
    {
	unset($this->db);
	unlink("/tmp/test.db");
    }
}
