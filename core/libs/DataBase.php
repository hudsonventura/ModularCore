<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class DataBase extends Core{

	public $conn;
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
				$this->conn = new \PDO(core::$coreConfig['databases'][$this->db]['vendor'].":dbname=".core::$coreConfig['databases'][$this->db]['dbname'].";host=".core::$coreConfig['databases'][$this->db]['host'], core::$coreConfig['databases'][$this->db]['user'], core::$coreConfig['databases'][$this->db]['pass']);
			}


			if(core::$coreConfig['databases'][$this->db]['vendor'] == 'oci'){
				$this->conn = new \PDO("oci:dbname="." (DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP) (HOST = ".core::$coreConfig['databases'][$this->db]['host'].")(PORT = ".core::$coreConfig['databases'][$this->db]['port'].")))(CONNECT_DATA = (".core::$coreConfig['databases'][$this->db]['connectData']." = ".core::$coreConfig['databases'][$this->db]['connectValue'].") (TNS = ".core::$coreConfig['databases'][$this->db]['tns'].")));".core::$coreConfig['databases'][$this->db]['charset'], core::$coreConfig['databases'][$this->db]['user'], core::$coreConfig['databases'][$this->db]['pass']);
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
		$this->query = 'SELECT '.$string.'
		';
	}

	 public function from($string){
		 $this->query = $this->query.' FROM '.$this->schema.$string.'
		';
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
		 $this->query = $this->query.' JOIN '.$this->schema.$string.' '.'
		';
	 }

	 public function rightjoin($string){
		 $this->query = $this->query.' RIGHT JOIN '.$this->schema.$string.' '.'
		';
	 }

	 public function leftjoin($string){
		 $this->query = $this->query.' LEFT JOIN '.$this->schema.$string.' '.'
		';
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
			// var_dump($stmt); die();
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
				try{
					while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
						array_push($return, $row);
					}
				} Catch(\PDOException $e){
					
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
	               $this->query = $this->query.'\''.$field.'\' ,';          //NUMERIC VALUE
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
	                 //consoleWrite($this->query.'<br />'.'Empty Set. Nothing returned from database.<br />');
	                 return false; //'Empty Set. Nothing returned from database';
	             }else{
	                 //consoleWrite($this->query.'<br />'.count($data).' row was displayed.<br />');
	                 return $data;
	             }
	         }else{ //TO WRITE A VALUE TO DB (UPDADE, INSERT, DELETE...)
	             $data =  $this->conn->exec($this->query);
	             //consoleWrite($this->query.'<br />'.$data. ' rows was affecteds.<br />');
				//
	             if($data==0){
	                 return "0 rows was affecteds.";
	             }else{
	                 return $data;
	             }
	         }
	     } catch (\PDOException $e) {
	         return false;
	     }


	 }

	 public function beginTransaction(){
		 return  $this->conn->beginTransaction();
	 }

	 public function commit(){
		 return  $this->conn->commit();
	 }

	 public function getQuery(){
	     return nl2br ($this->query);
	 }

	 public function query(){
	     return $this->query;
	 }

	 public function setQuery($query){
	     $this->query = $query;
	 }


	function __destruct() {
		$this->conn = null;
	}


}


class Orm
{

	private $query = '';
	public $stmt;
	public $lastError;
	private $return;
	

	public function getQuery()
	{
		return $this->query;
	}

	public function __construct(&$dbConnection){
		
		if (!is_a ($dbConnection, 'PDO')) {
			die('You have to send a PDO object');
		}
		$this->dbConnection = $dbConnection;
	}

	public function select($columns = '*'){
		$this->query = "SELECT $columns ";
		return $this;
	}

	public function from($table){
		$this->query .= "FROM $table ";
		return $this;
	}
	
	public function join($table){
		$this->query .= "JOIN $table ";
		return $this;
	}
	
	public function leftjoin($table){
		$this->query .= "LEFT JOIN $table ";
		return $this;
	}
	
	public function rightjoin($table){
		$this->query .= "RIGHT JOIN $table ";
		return $this;
	}

	public function limit($value){
		$this->query .= "LIMIT $value ";
		return $this;
	}

	public function offset($value){
		$this->query .= "OFFSET $value ";
		return $this;
	}

	public function where($value){
		$this->query .= "WHERE $value ";
		return $this;
	}

	public function orderby($value){
		$this->query .= "ORDER BY $value ";
		return $this;
	}

	public function groupby($value){
		$this->query .= "GROUP BY $value ";
		return $this;
	}
	

	
	public function insert($table, $array){
	     $this->query = 'INSERT INTO '.$table.' ( ';
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

	    return $this;
	}

	public function update($table, $array){
		$this->query = 'UPDATE '.$table.' SET ';
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
		return $this;
	}
	


	public function bindValue($collumn, $value){
		$this->prepare();
		try {
			switch ($value) {

				case is_integer($value) === 1:
					$return = $this->stmt->bindValue($collumn, $value, \PDO::PARAM_INT);
				break;

				case is_bool($value) === 1:
					$return = $this->stmt->bindValue($collumn, $value, \PDO::PARAM_BOOL);
				break;

				case is_null($value) === 1:
					$return = $this->stmt->bindValue($collumn, $value, \PDO::PARAM_NULL);
				break;

				case is_file($value) === 1:
					$return = $this->stmt->bindValue($collumn, $value, \PDO::PARAM_LOB);
				break;

				default:
					$return = $this->stmt->bindValue($collumn, $value, \PDO::PARAM_STR);
				break;
			}
		} catch (PDOException $e) {
			return $e;
		}
		return $return;
	}
	
	private function prepare(){
		if (!$this->stmt || $this->stmt->queryString <> $this->query) { 
			$this->stmt = $this->dbConnection->prepare($this->query);
		}
	}


	public function fetchOne($collumn = null){
		$success = $this->fetch();
		if ($success) {
			$return = $this->fetch();
			if (count($return)) {
				if ($collumn) {
					return $return[0][$collumn];
				}
				return $return[0];
			}
		}
		return false;
	}

	public function fetch(){
		$this->prepare();
		try{
			$success = $this->stmt->execute();
			if ($success) {
				return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			}
			return false;
		}catch(\PDOException $e){
			$this->lastError = $e;
		}
		return false;
	}

	public function execute(){
		$this->prepare();
		try{
			$success = $this->stmt->execute();
			if ($success) {
				return true;
			}
			return false;
		}catch(\PDOException $e){
			$this->lastError = $e;
		}
		return false;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}







	public function sync($file){

		if(!file_exists($file))
		{
			return false;
		}

		require_once($file);


		$lines = file ($file);

		//Class definition
		for ($i=0; $i < count($lines); $i++) {
			$line = $lines[$i];

			if (trim($line) == '#MCORM-Namespace') {
				$lineNamespace = $lines[$i+1];
				$lineNamespaceExploded = explode(' ', $lineNamespace);

				for ($i=0; $i < count($lineNamespaceExploded); $i++) { 
					if ($lineNamespaceExploded[$i] == 'namespace') {
						$namespace = explode (';', $lineNamespaceExploded[$i+1])[0];
					}
				}
			}

			if (trim($line) == '#MCORM-Table') {
				$lineClass = $lines[$i+1];

				$lineClassExploded = explode(' ', $lineClass);

				for ($i=0; $i < count($lineClassExploded); $i++) { 
					if ($lineClassExploded[$i] == 'class') {
						$className = $lineClassExploded[$i+1];
						goto ClassNamespaceDefined;
					}
				}

			}

		}

		ClassNamespaceDefined:


		
		if (!class_exists("\\$namespace\\$className"))
		{
			return false;
		}

		$table_exists = $this->verifyIfTableExists($className);
		if(!$table_exists){
			$created_table = $this->createTable($className);
		}

		$classAttributes = $this->getAttibutesFromClass($file);
		$dbAttributes = $this->getAttibutesFromdb($className);


		$attributes = $this->compareAttibutes($classAttributes, $dbAttributes);
		echo '<br>------------------------ > atributos do banco: <br>';
		var_dump($dbAttributes);

		echo '<br>------------------------ > atributos da classe: <br>';
		var_dump($classAttributes);


		var_dump($attributes);


		$result = $this->writeDataToDB($attributes);

		var_dumP($result);



	}
	private function verifyIfTableExists($table){
		$stmt = $this->dbConnection->prepare("select * from $table limit 1");

		try{
			$result = $stmt->execute();
			$return = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}catch(\PDOException $e){
			if (strpos($e->getMessage(), 'Undefined table')) {
				return false;
			}
		}
		if($result){
			return $table;
		}
		return false;
	}
	private function createTable($table){

		$stmt = $this->dbConnection->prepare("CREATE TABLE IF NOT EXISTS $table();");

		try{
			$result = $stmt->execute();
		}catch(\PDOException $e){
			echo $e->getMessage();
			return false;
		}
		if($result){
			return $table;
		}
		return false;
	}
	private function getAttibutesFromClass($file){

		$lines = file ($file);

		$columns = array();
		for ($i=0; $i < count($lines); $i++) {
			$line = $lines[$i];

			//Class definition
			if (!isset($class)) {
				if (trim($line) == '#MCORM-Table') {
					$lineTable = $lines[$i+1];
					$attributes = explode(' ', $lineTable);
					for ($j=0; $j < count($attributes); $j++) {
						if ($attributes[$j] == 'class') {
							$class = trim($attributes[$j+1]);
							array_push($columns, array('table' => $class));
						}
					}
				}
			}

			//Attributes definition
			if (strpos($line, '#MCORM-Attribuite') > 0)
			{
				$lineComponent = explode('|', $line);
				for ($j=0; $j < count($lineComponent); $j++) {
					$column = array();
					if (trim($lineComponent[$j]) == '#MCORM-Attribuite') {

						//get column name
						$lineAttributeName = $lines[$i+1];
						$lineAttributeNameComponents = explode(' ', $lineAttributeName);
						foreach ($lineAttributeNameComponents as $attribute) {
							if (substr($attribute, 0, 1) == '$') {
								if(strpos($attribute, ';')){
									$column['name'] = substr($attribute, 1, strpos($attribute, ';')-1);
								}else{
									$column['name'] = substr($attribute, 1);
								}

							}
						}

						//get everything else
						$column['type']         = trim($lineComponent[1]);
						$column['lenght']         = trim($lineComponent[2]);
						$column['canbenull']    = trim($lineComponent[3]);
						$column['primarykey']   = trim($lineComponent[4]);
						$column['defaultvalue']  = trim($lineComponent[5]);

						array_push($columns, $column);
					}
				}
			}
		}
		return $columns;
	}
	private function getAttibutesFromdb($className){
		$table = strtolower(trim($className));

		$query = "SELECT column_name FROM information_schema.columns WHERE table_name =:table;";
		$stmt = $this->dbConnection->prepare($query);

		$stmt->bindValue(':table', $table, \PDO::PARAM_STR);
		try{
			$result = $stmt->execute();
			$return = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		}catch(\PDOException $e){
			return $e->getMessage();
		}
		return $return;
	}
	private function compareAttibutes($classAttributes, $dbAttributes){
		$attributesAlter = array();
		$attributesAdd = array();
		$attributesRemove = array();

		$classAttributeClean = array();
		for ($i=1; $i < count($classAttributes); $i++)
		{
			$classAttribute = $classAttributes[$i]['name'];

			if (count($dbAttributes) > 0) {
				foreach($dbAttributes as $dbAttribute)
				{
					if (!in_array($classAttribute, $attributesAdd)) {
						array_push($attributesAdd, $classAttribute);
					}

					if ($classAttribute == $dbAttribute) {
						array_push($attributesAlter, $classAttribute);
					}
				}
			}else{
				array_push($attributesAdd, $classAttribute);
			}

		}
		$attributesAdd = array_diff($attributesAdd, $dbAttributes);
		$attributesRemove = array_diff($dbAttributes, $attributesAdd);
		$attributesRemove = array_diff($attributesRemove, $attributesAlter);

		$returnAttributesAdd = array();
		for ($i=1; $i < count($classAttributes); $i++)
		{
			if (in_array($classAttributes[$i]['name'], $attributesAdd)) {
				array_push($returnAttributesAdd, $classAttributes[$i]);
			}
		}

		$returnAttributesAlter = array();
		for ($i=1; $i < count($classAttributes); $i++)
		{
			if (in_array($classAttributes[$i]['name'], $attributesAlter)) {
				array_push($returnAttributesAlter, $classAttributes[$i]);
			}
		}
		return array('table'=> strtolower($classAttributes[0]['table']), 'add'=> $returnAttributesAdd , 'alter'=> $returnAttributesAlter , 'remove'=> $attributesRemove);
	}
	private function writeDataToDB($attributes){
		$table = $attributes['table'];
		$add = $attributes['add'];
		$alter = $attributes['alter'];
		$remove = $attributes['remove'];


		$query = 'ALTER TABLE '.$table.' ';
		//remove
		if (count($remove) > 0) {
			foreach ($remove as $removeColumn) {
				$query .= 'DROP COLUMN "'.$removeColumn.'", ';
			}
		}

		$primaryKey = '';

		//alter
		if (count($alter) > 0) {
			foreach ($alter as $alterColumn) {

				switch ($alterColumn['type'] ) {
					case 'SERIAL':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE int'.$alterColumn['lenght'].', ';
						$primaryKey = 'ADD PRIMARY KEY ('.$alterColumn['name'].')  ';
					break;

					case 'VARCHAR':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE VARCHAR('.$alterColumn['lenght'].') COLLATE "default", ';
					break;

					case 'CHAR':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE CHAR('.$alterColumn['lenght'].') COLLATE "default", ';
					break;

					case 'INT':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE INT'.$alterColumn['lenght'].', ';
					break;

					case 'BOOLEAN':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE BOOL, ';
						break;

					case 'FLOAT':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE FLOAT'.$alterColumn['lenght'].' USING '.$alterColumn['name'].'::real, ';
						break;


					case 'TEXT':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE TEXT, ';
						break;

					case 'DATE':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE DATE, ';
						break;

					case 'TIME':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE TIME, ';
						break;


					case 'TIMESTAMP':
						$query .= 'ALTER COLUMN "'.$alterColumn['name'].'" TYPE TIMESTAMP, ';
						break;

					default:
						$query .= ' ##THIS EDITING A NEW TYPE ('.$alterColumn['type'].'). PLEASE SEE THE DOCUMENTATION## ';
					break;




				}


				if ($alterColumn['canbenull'] == 'NOT NULL') {
					$query .= ' ALTER COLUMN "'.$alterColumn['name'].'" SET NOT NULL, ';
				}else{
					$query .= ' ALTER COLUMN "'.$alterColumn['name'].'" DROP NOT NULL, ';
				}

				if ($alterColumn['defaultvalue'] <> 'NULL') {
					$query .= ' ALTER COLUMN "'.$alterColumn['name'].'" SET DEFAULT '.$alterColumn['defaultvalue'].', ';
				}


			}
		}


		//add
		if (count($add) > 0) {
			foreach ($add as $addColumn) {
				$query .= 'ADD COLUMN "'.$addColumn['name'].'" ';


				switch ($addColumn['type']) {
					case 'SERIAL':
						$query .= 'SERIAL'.$addColumn['lenght'].' ';
						$primaryKey = 'ADD PRIMARY KEY ('.$addColumn['lenght'].')  ';
						break;

					case 'VARCHAR':
						$query .= 'VARCHAR('.$addColumn['lenght'].') ';
						break;

					case 'INT':
						$query .= 'int'.$addColumn['lenght'].' ';
						break;

					case 'CHAR':
						$query .= 'CHAR('.$addColumn['lenght'].') ';
						break;

					case 'BOOLEAN':
						$query .= 'BOOL ';
						break;

					case 'FLOAT':
						$query .= 'FLOAT'.$addColumn['lenght'].' ';
						break;


					case 'TEXT':
						$query .= 'TEXT ';
						break;

					case 'DATE':
						$query .= 'DATE ';
						break;

					case 'TIME':
						$query .= 'TIME('.$addColumn['lenght'].') ';
						break;


					case 'TIMESTAMP':
						$query .= 'TIMESTAMP('.$addColumn['lenght'].') ';
						break;

					default:
						$query .= ' ##THIS ADDING A NEW TYPE ('.$addColumn['type'].').. PLEASE SEE THE DOCUMENTATION## ';
						break;
				}

				$addColumn['type'];


				if ($addColumn['canbenull'] == 'NOT NULL') {
					$query .= $addColumn['canbenull'];
				}
				$query .= ', ';

				if ($addColumn['defaultvalue'] <> 'NULL') {
					$query .= ' ALTER COLUMN "'.$addColumn['name'].'" SET DEFAULT '.$addColumn['defaultvalue'].', ';
				}

			}
		}
		$query = substr($query, 0, strlen($query.$primaryKey)-2);


		
		VAR_DUMP($primaryKey); DIE();

		echo nl2br($query);

		$stmt = $this->dbConnection->prepare($query);
		$result = $stmt->execute();
		$return = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		var_dump($result);
		var_dump($return);
	}
}