<?php
/* PHP Class for connecting to database
 * AUTHOR: Antony Acosta, Modified by Mickael Souza
 * LAST EDIT: 2018-12-02
 */
namespace App\Models;

use \PDO;
use \PDOException;


class Connection 
{
    
    private $connection;
    private $user;
    private $password;
    
    public function __construct($driver, $user, $password, $dbname, $host, $charset = "utf8")
    {
        $connectionString = "{$driver}:host={$host};dbname={$dbname};charset={$charset}";
        $this->user     = $user;
        $this->password = $password;

        try {
        	$this->connection = new PDO($connectionString, $this->user, $this->password);
        } catch(PDOException $e) {
        	throw new Exception($e->getMessage());
        }
        
    }
    
    public function exec($query, $callback, $callback_param = null, $params = null)
    {
        try{
            
            $this->connection->beginTransaction();
            $preparedStatement = $this->connection->prepare($query);
          
            if($params !== null){
                foreach($params as $key=>$value){
                    $preparedStatement->bindValue(":{$key}",$value);
                }
            }
            
            if($preparedStatement->execute() === false){
                throw new PDOException($preparedStatement->errorCode());
            }
            
            if(method_exists($preparedStatement,$callback)){

                $return = $preparedStatement->$callback();

                if($callback_param) {
            		$return = $preparedStatement->$callback($callback_param);
            	}
                
            }elseif(method_exists($this->connection, $callback)){

                $return = $this->connection->$callback();
                
                if($callback_param) {
            		$return = $this->connection->$callback($callback_param);
            	}

            }else{
                $return = $preparedStatement->rowCount();
            }            
            
            $this->connection->commit();
            return $return;
            
        }catch(PDOException $exc){
            if(isset($this->connection) && $this->connection->inTransaction()){
                $this->connection->rollBack();
            }
            die("Error: ".$exc->getCode()." ".$exc->getMessage());
        }
    }
    
    
    
    
}
