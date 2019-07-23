<?php  

namespace App\Models;
use App\Models\Connection;

class Model
{
	private $connection;
	private $query;
	private $class;
	private $props = [];
	private $params = [];
	private $values;
	private $return_one = false;
	private $table_name;

    public function __construct($config = null) {

        if(!$config){
           $config = parse_ini_file("config.ini");
        }

        $this->connection = new Connection(
	        $config['driver'],
	        $config['user'],
	        $config['password'],
	        $config['dbname'],
	        $config['host'],
	        $config['charset']
        );

        $this->class = new \ReflectionClass($this);

       	$this->setProps();

        $this->values = array_map(function($e) {
        	return ":{$e}";
        }, $this->props);

        $this->table_name = strtolower($this->class->getShortName()).'s';
       
    }

    private function setProps() {
    	foreach ($this->class->getProperties() as $prop) {
        	$this->props[] = $prop->getName();
        }

    }

    private function setParams() 
    {
    	foreach ($this->class->getProperties() as $prop) {
        	$this->params["{$prop->getName()}"] = $this->class->getProperty($prop->getName())->getValue($this);
        }
    }

    public function setPropertiesByArray(array $array) 
    {
    	foreach ($array as $name => $value) {
    		if(property_exists($this, $name)) {
    			$this->{$name} = $value;
    		}
    	}

    	$this->params = [];

    	return $this;
    }

    public function insert() 
    {
    
    	if($this->params == []) {
    		$this->setParams();
    	}

    	$props = implode(",", $this->props);
        $values = implode(",", $this->values);


    	$this->query = "INSERT INTO {$this->table_name} ({$props}) VALUES ({$values})";

    	return $this;
    }

    public function update($isNotId = false)
    {
    	$where = "";

    	$this->setParams();

    	$params = array_map(function($e) {
    		return "{$e} = :{$e}";
    	}, $this->props);

    	$params = implode(", ", $params);

    	if(!$isNotId) {
    		$where = "WHERE id = '{$this->id}'";
    	}
    	
    	$this->query = "UPDATE {$this->table_name} SET {$params} {$where}";

    	return $this;
    }

    public function delete($isNotId = false)
    {
    	$where = "";

    	$this->params = null;

    	if(!$isNotId) {
    		$where = "WHERE id = '{$this->id}'";
    	}

    	$this->query = "DELETE FROM {$this->table_name} {$where}";

    	return $this;
    }

    public function select(array $params = null) 
    {
    	$this->params = null;
    	$this->query = "";

    	$params = $params != null ? implode(",", $params) : "*";

    	$this->query = "SELECT {$params} FROM {$this->table_name}";

    	return $this;
    }

    public function where(array $params)
    {

    	$params = array_map(function($key, $value) {
    		return "{$key} = '{$value}'";
    	}, array_keys($params), $params);

    	$params = implode(", ", $params);

    	$this->query.= " WHERE {$params}";
    	
    	return $this;

    }

    public function query($query) {

    	$this->query = $query;

    	return $this;
    }

    public function returnArray() 
    {
    	return $this->run("fetchAll");
    }

    public function returnArrayAssoc()//Only 1 result 
    {
    	return $this->run("fetch", PDO::FETCH_ASSOC);
    }

    public function returnJson($one_result = false) 
    {
    	$array = $this->returnArray();

    	if($one_result) {
    		$array = $this->returnArrayAssoc();
    	}

    	return json_encode($array);
    }

    public function execute()//For Select without return
    {
    	$this->setPropertiesByArray($this->run("fetch")); 

    	return $this;
    }

    public function save()
    {
    	return $this->run(); 
    }

    private function run($callback = "rowCount", $callback_param = null)
    {
    	return $this->connection->exec($this->query, $callback, $callback_param, $this->params);
    }

}

