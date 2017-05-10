<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class DataBase extends Core{

	private $conn;
	private $db;
	public $vendor;
	public $sqlBuilder;
	public $orm;

	function __construct($db) {
		parent::__construct();

		$this->db = $db;
		if(!isset(core::$coreConfig['databases'][$this->db]['vendor'])){
			$error = 'The database called <b>'.$this->db.'</b> doesn\'t exists in the config file.';
			include '/core/errors/402.php';
			die();
		}
		$this->vendor = core::$coreConfig['databases'][$this->db]['vendor'];
		$this->sqlBuilder = new SQLBuilder($db);
	}
}

class DataBaseConnector extends Core{

	protected $schema;

	function __construct($db) {
		parent::__construct();
		$this->db = $db;

		if(isset(core::$coreConfig['databases'][$this->db]['schema'])){
			$this->schema = core::$coreConfig['databases'][$this->db ]['schema'];
		}else
			$this->schema = '';


		//bancos com schema não oracle
		if($this->schema <> '' && core::$coreConfig['databases'][$this->db ]['vendor'] <> 'oci')
			if(strpos($this->schema, '"') == false){
				$this->schema = '"'.$this->schema.'"';
			if(strpos($this->schema, '.') == false)
				$this->schema .= '.';
		}

		//bancos com schema ORACLE
		if($this->schema <> '' && core::$coreConfig['databases'][$this->db ]['vendor'] == 'oci'){
			$this->schema .= '.';
		}
	}

	protected function connect(){
		try{

			if(core::$coreConfig['databases'][$this->db]['vendor'] <> 'oci'){
				$this->conn = new \PDO(core::$coreConfig['databases'][$this->db]['vendor'].":dbname=".core::$coreConfig['databases'][$this->db]['dbname'].";host=".core::$coreConfig['databases'][$this->db]['host']."", core::$coreConfig['databases'][$this->db]['user'], core::$coreConfig['databases'][$this->db]['pass']);
			}


			if(core::$coreConfig['databases'][$this->db]['vendor'] == 'oci'){
				$this->conn = new \PDO("oci:dbname="." (DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP) (HOST = ".core::$coreConfig['databases'][$this->db]['host'].")(PORT = ".core::$coreConfig['databases'][$this->db]['port'].")))(CONNECT_DATA = (".core::$coreConfig['databases'][$this->db]['connectData']." = ".core::$coreConfig['databases'][$this->db]['connectValue'].") (TNS = ".core::$coreConfig['databases'][$this->db]['tns'].")))", core::$coreConfig['databases'][$this->db]['user'], core::$coreConfig['databases'][$this->db]['pass']);
			}



			$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} Catch(\PDOException $e){
			$error = $e->getMessage();
			if(debug_backtrace()[1] = 'ModularCore\SQLBuilder'){
				include 'core/errors/401.php';
			die();
			}
			//include 'core/errors/401.php';
			//die();
		}
	}
}


class SQLBuilder extends DataBaseConnector{

	private $query;

	function __construct($db) {
		parent::__construct($db);
		$this->connect();
	}

	public function select($string){
		$this->query = 'SELECT '.$string;
	}

	 public function from($string){
		$this->query = $this->query.' FROM '.$this->schema.$string;
	 }

	 public function where($string){
		$this->query = $this->query.' WHERE '.$string.' ';
	 }

	 public function whereand($string){
		$this->query = $this->query.' AND '.$string.' ';
	 }

	 public function whereor($string){
		$this->query = $this->query.' OR '.$string.' ';
	 }

	 public function on($string){
		$this->query = $this->query.' ON '.$string.' ';
	 }

	public function delete(){
		$this->query = 'DELETE ';
	}

	 public function join($string){
		$this->query = $this->query.' JOIN '.$this->schema.$string.' ';
	 }

	 public function rightjoin($string){
		$this->query = $this->query.' RIGHT JOIN '.$this->schema.$string.' ';
	 }

	 public function leftjoin($string){
		$this->query = $this->query.' LEFT JOIN '.$this->schema.$string.' ';
	 }

	 public function offset($string){
		$this->query = $this->query.' OFFSET '.$string.' ';
	 }

	 public function limit($string){
	 	//bancos não oracle
		if(core::$coreConfig['databases'][$this->db ]['vendor'] <> 'oci'){
			$this->query = $this->query.' LIMIT '.$string.' ';
		}else{
			//bancos ORACLE
			if(strpos(strtolower($this->query), 'where') == false){
				$this->query = $this->query.' WHERE ROWNUM <= '.$string.' ';
			}else{
				$this->query = $this->query.' AND ROWNUM <= '.$string.' ';
			}

		}

	 }
	 public function orderby($string){
		$this->query = $this->query.' ORDER BY '.$string.' ';
	 }
	 public function groupby($string){
		$this->query = $this->query.' GROUP BY '.$this->schema.$string.' ';
	 }

	 public function count($string){
	 	$this->query = "SELECT COUNT($string)";
	 }

	 public function executeQuery(){
	 	//var_dump($this->query.' ----> '.strpos($this->query, 'SELECT ').strpos($this->query, ' FROM '));
	 	if(strpos($this->query, 'SELECT ') === false && strpos($this->query, ' FROM ') ===false ){
	 		$stmt = $this->conn->prepare($this->query);
	 		try
			{
				$return = $stmt->execute();
			}
			catch (Exception $exception)
			{
				echo '<h2>ERROR 403 - FORBIDDEN</h2> Error when try query <b>'.$this->query.'</b> in the file <b>'.debug_backtrace()[1] ['file'].'</b> in the line <b>'.debug_backtrace()[1] ['line'].'<br>Database error:</b> '.$exception->getMessage();
			}
			 return $return;


	 		//return $this->conn->execute($this->query);
	 	}else{
	 		//var_dump($this->query.' ----> '.strpos($this->query, 'SELECT ').strpos($this->query, ' FROM '));
	 		try{
				$result = $this->conn->query($this->query);
			} Catch(\PDOException $e){
				echo '<h2>ERROR 403 - FORBIDDEN</h2> Error when try query <b>'.$this->query.'</b> in the file <b>'.debug_backtrace()[1] ['file'].'</b> in the line <b>'.debug_backtrace()[1] ['line'].'<br>Database error:</b> '.$e->getMessage();
				return false;
			}

			if($result){
				$return = array();
				while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
					array_push($return, $row);
				}

				return $return;
			}
			return false;
	 	}


	}

	 public function insert($array, $table){
	     $this->query = 'INSERT INTO '.$this->schema.$table.' ( ';
	     foreach(array_keys($array) as $field){
	         $this->query = $this->query.'"'.$field.'"'.' ,';
	     }
	     $this->query = substr($this->query, 0, strlen($this->query)-2).') VALUES (';
	     foreach(array_values($array) as $field){
	           if(!is_numeric($field) && !is_float($field)){
	               if(strpos($field, 'nextval') > -1){
	                   $this->query = $this->query.'"'.$field.'"'.' ,';      //NEXTVAL STRING (Postgres)
	               }else{
	                   $this->query = $this->query."'".$field."' ,"; //COMM STRING
	               }

	           }else{
	               $this->query = $this->query.$field.' ,';          //NUMERIC VALUE
	           }

	     }
	     $this->query = substr($this->query, 0, strlen($this->query)-2).')';

	     return $this->query;
	 }

	 public function update($array, $table){
	   $this->query = 'UPDATE '.$this->schema.$table.' SET ';
		for ($i=0; $i<= count($array)-1; $i++){
			if(is_string(array_values($array)[$i])){
				$this->query = $this->query.'"'.array_keys($array)[$i].'"'." = '".array_values($array)[$i]."', ";
			}else{
				if(is_numeric(array_values($array)[$i]) || is_float(array_values($array)[$i])){
					$this->query = $this->query.'"'.array_keys($array)[$i].'"'." = ".array_values($array)[$i].", ";
					}else{
						if(is_null(array_values($array)[$i])){
							$this->query = $this->query.'"'.array_keys($array)[$i].'"'." = NULL, ";
						}else{
							if(array_values($array)[$i] == ''){
								$this->query = $this->query.'"'.array_keys($array)[$i].'"'." = NULL, ";
							}else{
								$this->query = $this->query.'"'.array_keys($array)[$i].'"'." = ".array_values($array)[$i].", ";
							}
						}
				}
			}
	       }

	   $this->query = substr($this->query, 0, strlen($this->query)-2);

	   return $this->query;
	 }

	 public function execute(){
	   $this->query = $this->query.';';

	     try {
	     		//TO READ A VALUE FROM DB (SELECT)
	         if(strpos($this->query, 'SELECT')>-1 || strpos($this->query, 'JOIN')>-1){
       				  //var_dump($this->conn);
	             $data = $this->conn->query($this->query, \PDO::FETCH_ASSOC);
	             //$data = $this->conn->query($this->query, \PDO::FETCH_ASSOC);//TODO: Provavel erro --------------------------------------
	             $data = $data->fetchAll();
	             if(count($data)==0){
	                 consoleWrite($this->query.'<br />'.'Empty Set. Nothing returned from database.<br />');
	                 return 'Empty Set. Nothing returned from database';
	             }else{
	                 //consoleWrite($this->query.'<br />'.count($data).' row was displayed.<br />');
	                 return $data;
	             }
	         }else{ //TO WRITE A VALUE TO DB (UPDADE, INSERT, DELETE...)
	             $data =  $this->conn->exec($this->query);
	             consoleWrite($this->query.'<br />'.$data. ' rows was affecteds.<br />');
				//
	             if($data==0){
	                 return "0 rows was affecteds.";
	             }else{
	                 return $data;
	             }
	         }
	     } catch (\PDOException $e) {
	         //consoleWrite($e);
	         return 'Fail to execute your query.';
	     }


	 }

	 public function getQuery(){
	     return $this->query.';';
	 }

	 public function setQuery($query){
	     $this->query = $query;
	 }


	function __destruct() {
		$this->conn = null;
	}


}
