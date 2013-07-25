class DBQueries
{
    protected $num_results = 0;

	protected function selectQuery($sql,$search) {
	
		/* DBQueries->selectQuery($sql,$search)
		* Description:
		*   Base function for selected data from the database.
		* Parameters:
		*   $sql - a string containing the SQL statement
		*			customized for whatever purpose.
		*   $search - a keyed array containing all of the
		*			named parameters for the SQL statement
		* Sample Usage:
		*   $DB_class = new DBQueries;
		*	$item_array = $DB_class->selectQuery("SELECT * FROM Buildings","");
		* Return Argument:
		*   returns a keyed array of the database query results.
		*/
	
		$conn = new DBH();
		$DBH = $conn->getConnection();
		$result = array();
		$i = 0;
	
		try {
			$STH = $DBH->prepare($sql);
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			if (empty($search)) {
				$STH->execute();	
			} else {
				$STH->execute($search);
			}
			while ($row = $STH->fetch()) {
				foreach ($row as $key=>$value) {
				    $result[$i][$key] = $value;
				}
				
				$i++;
			}
			$this->num_results = $i;
			return $result;
		} catch (PDOException $e) {
			$message = $e->getMessage();
			$conn->dberror($message, $sql);
		}
	}
	protected function insertUpdateDeleteQuery($sql,$changes) {
	
		/* DBQueries->insertUpdateDeleteQuery($sql,$changes)
		* Description:
		*   Base function for inserting, updating, or
		*	deleting data from the database.
		* Parameters:
		*   $sql - a string containing the SQL statement
		*		customized for whatever purpose.
		*   $changes - a keyed array containing all of the named
		*			parameters for the SQL statement
		* Sample Usage:
		*   $DB_class = new DBQueries;
		*	$item_array = $DB_class->insertUpdateDeleteQuery($sql,$changes);
		* Return Argument:
		*   returns a bool value of the success of the performed query.
		*/
	
		$conn = new DBH();
		$DBH = $conn->getConnection();
		$result = false;
	
		try {
			$STH = $DBH->prepare($sql);
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$row = $STH->execute($changes);
			if ($row == true) {
				$result = true;
			} else {
				$result = false;
			}
	
			return $result;
		} catch (PDOException $e) {
			$message = $e->getMessage();
			$conn->dberror($message, $sql);
		}
	}
}
