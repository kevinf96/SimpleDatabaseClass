<?php
class Database extends PDO {
	private $db; //PDO database instance
	private $cacheExpire = 60*5; //Cache time in seconds
	public $queryCount = 0; //Counts the Querys executed


	/**
	 * Construct function.
	 *
	 * Init the MySQL connection.
	 *
	 * @param string $host MySQL-Server IP address.
	 * @param string $user MySQL user.
	 * @param string $database MySQL database (optional).
	 * @param string $port MySQL port (optional).
	 */	
	public function __construct($host,$user,$passwd,$database="",$port = 3306) {
	
		try
		{    
			if(!is_null($database)){
				$this->db = new PDO("mysql:host=$host;port=$port;charset=utf8;dbname=$database", $user, $passwd);
			}
			else{
				$this->db = new PDO("mysql:host=$host;port=$port;charset=utf8;", $user, $passwd);
			} 
			$this->db->exec("set names utf8");
		}
		catch (PDOException $e)
		{
			exit( "Verbindung zur Datenbank konnte nicht hergestellt werden".$e);
		}	
	}

	/**
	 * Query function.
	 *
	 * Execute a query with boolean result
	 *
	 * @param string $query MySQL query.
	 * @param array $param Prepared statements array.
	 * @return boolean Returns if the query was successfully executed
	 */		
	public function query($query, $param = array()) {
		try{
			$this->queryCount++;
			if(empty($query))
				return;	
			return $this->db->prepare($query)->execute($param);
		}
		catch(Exception $e) {
			echo 'Exception -> ';
			print_r($this->db->errorInfo());
		}
	}
	
	/**
	 * Get function.
	 *
	 * Execute a query with returning data
	 *
	 * @param string $query MySQL query.
	 * @param array $param prepared statements array.
	 * @param boolean $cache If cache should be enabled for this query.
	 * @return object Returns the result as an object.
	 */		
	public function get($query, $param = array(),$cache = true) {
		if(empty($query))
			return array();
			
		$result = $cache ? phpFastCache::get(md5($query.implode($param))) : null;
		if(is_null($result)) {
			$result = $this->db->prepare($query);
			$result->execute($param);
			$result = $result->fetchAll(PDO::FETCH_OBJ);
			if($cache){
				phpFastCache::set(md5($query.implode($param)), $result, $this->cacheExpire);
			}
			$this->queryCount++;
		}
		return $result;
	}
	
	/**
	 * Delete function
	 *
	 * Deletes an instance of the class
	 *
	 * @return boolean.
	 */		
	public function delete(){
		return $this->db = null;
	}

	/**
	 * GetError function
	 *
	 * returns an array with information about querys which failed
	 *
	 * @return array.
	 */		
	public function getError(){
		return $this->db->errorInfo();
	}
}
?>