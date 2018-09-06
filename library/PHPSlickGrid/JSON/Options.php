<?php


class PHPSlickGrid_JSON_Options  extends PHPSlickGrid_JSON_Abstract {
	
// 	private $Table = null;
// 	private $Config = null;
// 	private $TableName = null;
// 	private $PrimaryKey = null;
// 	private $UpdatedColumn = null;
// 	private $parameters = null;
	
// 	function __construct() {
	
// 		// Get our logger for debugging
// 		$this->log = Zend_Registry::get('logger');
	
// 		// Get Parameters passed to PHPSlickGrid_RPC_DbTable
// 		$this->parameters=Zend_Registry::get('PHPSlickGrid_JSON_DbTable_DataAccess');
// 		$TableClassName=$this->parameters['table'];
// 		$this->Table=new $TableClassName();
	
// 		// Get our table configuration
// 		$this->Config = $this->parameters['config'];
	
// 		// Get some info about our table
// 		$this->TableName=$this->Table->info('name');
// 		$info=$this->Table->info();
// 		$this->PrimaryKey=array_shift($info['primary']);
	
// 		// Get Date Time column
// 		$info = $this->Table->info('metadata');
// 		foreach($info as $key=>$value) {
// 			if (($value['DEFAULT']=='CURRENT_TIMESTAMP')&&($value['DATA_TYPE']=='timestamp')) {
// 				$this->UpdatedColumn=$key;
// 				break;
// 			}
// 		}
	
// 	}
	
	function Options_Ver() {
		return ('2.0');
	}
	
	/**
	 * Send Back a list of drop down options
	 * based on the column selected and the value of the other rows
	 *
	 * @param  array $row
	 * @param  array $grid_name
	 * @return array
	 */
	public function getOptions($column, $item) {
		try {
	
			// $parameters=array_merge_recursive($options,$this->parameters);
	
			$MethodName = "Options_".$column; // Call the method in the Db_Table that is "Options_(Column Name)".
	
			if (method_exists($this->Table,$MethodName))
				$Rows=$this->Table->$MethodName($item,$this->Config->project_id);// Set our data to the database options
			else
				throw new Exception("No method ".$MethodName);
	
			return $Rows;
	
		}
		catch (Exception $ex) {
			throw new Exception(print_r($ex,true), 32001);
		}
	
	}
	
}