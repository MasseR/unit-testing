<?php

require_once("inc/NotFoundException.php");

abstract class DatabaseHelper
{
    protected $db = NULL;
    public function interfaceDatabase(Database $db)
    {
	$this->db = $db;
    }
    // Must handle existing database
    abstract public function create();
}

class LocationDatabase extends DatabaseHelper
{
    public function create()
    {
	try
	{
	    $this->db->exec("CREATE TABLE Location (id INTEGER PRIMARY KEY AUTOINCREMENT, location Text)");
	} catch(Exception $e)
	{
	}
    }
}

class Database
{
    private static $instance = null;
    private $filename;
    private $db;

    public function __construct($file, DatabaseHelper $x)
    {
	$this->filename = $file;
	$this->db = new PDO("sqlite:/" . $file);
	$x->interfaceDatabase($this);
	$x->create();
    }

    public function exec($query)
    {
	$this->db->exec($query);
    }

    private function bind_helper($array)
    {
	if(count($array) == 0 || array_key_exists(0, $array))
	    return $array;
	return array($array);
    }

    private function bind($stmt, $bind)
    {
	array_map(function($key, $value) use($stmt) {
	    return $stmt->bindParam($key, $value);
	}, array_keys($bind), array_values($bind));
	return $stmt;
    }

    private function bind_execute($query, $values, $func)
    {
	$retarray = array();
	$stmt = $this->db->prepare($query);
	foreach($this->bind_helper($values) as $b)
	{
	    $stmt = $this->bind($stmt, $b);
	    if($stmt->execute())
		$retarray[] = call_user_func($func, $stmt);
	}
	return $this->clean_results($retarray);
    }

    public function bind_query($query, $values)
    {
	return $this->clean_results($this->bind_execute($query, $values, array($this, 'iterate_results')));
    }

    private function iterate_noop($ret)
    {
	return array();
    }

    private function iterate_results($stmt)
    {
	$ret = $stmt->fetchAll();
	if(count($ret) == 1)
	    return $ret[0];
    }

    private function clean_results($retarray)
    {
	if($retarray === NULL)
	    throw new NotFoundException();
	if(count($retarray) == 1)
	{
	    $ret = $retarray[0];
	    return $ret;
	}
	return array_filter($retarray, function($x) { return $x !== FALSE; });
    }

    public function query($query)
    {
	$ret = $this->db->query($query);
	return $this->clean_results($ret->fetchAll());
    }

    public function bind_exec($query, $values)
    {
	return $this->bind_execute($query, $values, array($this, "iterate_noop"));
    }

    public function getPath()
    {
	return $this->filename;
    }

    public static function newInstance($file, DatabaseHelper $x)
    {
	if(self::$instance === NULL)
	    self::$instance = new Database($file, $x);
	return self::$instance;
    }
}


function db()
{
    return Database::newInstance("/tmp/real.db", new LocationDatabase());
}
