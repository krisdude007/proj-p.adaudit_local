<?php


class PHPSlickGrid_JSON_Meta  extends PHPSlickGrid_JSON_Abstract {
	
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
	
	function Meta_Ver() {
		return ('2.0');
	}
	
	/**
	 * Send Back a list of drop down options
	 * based on the available roles.
	 *
	 * @return array
	 */
	public function getRoles() {
		try {
			$acl= Zend_Registry::get('acl');
	
			return $acl->getRoles();
		}
		catch (Exception $ex) {
			throw new Exception(print_r($ex,true), 32001);
		}
	}
	
	/**
	 * Send Back a list of drop down options
	 * based on the available roles.
	 *
	 * @return array
	 */
	public function getPremissions() {
		try {
			$acl= Zend_Registry::get('acl');
	
			return $acl->getRoles();
		}
		catch (Exception $ex) {
			throw new Exception(print_r($ex,true), 32001);
		}
	}
	
	/**
	 * Get Meta data for the column
	 *
	 * @param  string $column_name
	 * @return array
	 */
	public function getMeta($column_name) {
		try {
			//$this->Config->project_id
			//$this->Config->gridName
			$MetaTable = new Application_Model_DbTable_ColumnMeta();
	
			//$this->log->debug($this->Config->project_id);
			//$this->log->debug($this->Config->gridName);
	
			$sel = $MetaTable->select();
			$sel->where("project_id = ?",$this->Config->project_id);
			$sel->where("grid_nm = ?",$this->Config->gridName);
			$sel->where("column_nm = ?",$column_name);
	
			$row=$MetaTable->fetchRow($sel);
	
			if (!$row)
				$row=$MetaTable->createRow();
	
			//$this->log->debug($row->toArray());
	
			return ($row->toArray());
			//return array();
		}
		catch (Exception $ex) {
			throw new Exception(print_r($ex,true), 32001);
		}
	}
	
	
	/**
	 * Get Meta data for the column
	 *
	 * @param  string $column_name
	 * @return array
	 */
	public function saveMeta($data) {
		try {
			$MetaTable = new Application_Model_DbTable_ColumnMeta();
	
			$row=null;
			if ($data['column_meta_id']==null)
				$row=$MetaTable->createRow();
			else
				$row=$MetaTable->find($data['column_meta_id'])->current();
	
			foreach($data as $key=>$value) {
				$row->$key=$value;
			}
			$row->project_id = $this->Config->project_id;
			$row->grid_nm = $this->Config->gridName;
			$row->save();
			 
		}
		catch (Exception $ex) {
			throw new Exception(print_r($ex,true), 32001);
		}
	}
	
}