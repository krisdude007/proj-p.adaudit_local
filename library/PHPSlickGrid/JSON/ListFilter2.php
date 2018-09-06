<?php


class PHPSlickGrid_JSON_ListFilter2 extends PHPSlickGrid_JSON_Abstract {
	
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
	
	function ListFilter2_Ver() {
		return ('2.0');
	}
	
	/**
	 * get distinct values for a row
	 *
	 * @param string row
	 * @param array options
	 * @return array
	 */
	public function getBlockDistinct($block, $options) {

		try
		{
	
			$parameters=array_merge_recursive($options,$this->parameters);
	
			$column=$options['columnDef']['field'];
	
	
			$sel = $this->Table->select();
			$this->addConditionsToSelect($this->Config->conditions, $sel);
			$this->createWhere($sel, $options['where_list']);
			if (!empty($options['quicksearch']))
				$sel->where("$column LIKE ?",'%'.$options['quicksearch'].'%');
			//$sel->from($this->TableName, array(new Zend_Db_Expr("$column")));
			$sel->from($this->TableName, array('value'=>$column));
			$sel->distinct();
			$sel->limit($options['blockSize'],$block*$options['blockSize']);
	
			// Build our order by
			$sel->order(array($column));
	
			$Results = $this->Table->fetchAll($sel);
	
	
			if ($Results) {
				//$this->log->debug($Results->toArray());
				return ($Results->toArray());
			}
	
			return array();
		}
		catch (Exception $ex) {
			throw new Exception($ex, 32001);
		}
		
		return null;
	
	}
	
	/**
	 * get distinct values for a row
	 *
	 * @param string row
	 * @param array options
	 * @return int
	 */
	public function getDistinctLength($column, $options) {
		try
		{
			//select  count( distinct `tail_num`) from coding
			//$parameters=array_merge($options,$this->parameters);
	
			//throw new Exception(print_r($column,true));
			$Results = 0;
	
			//$this->log->debug($this->TableName);
	
			$sel=$this->Table->select();
			$this->addConditionsToSelect($this->Config->conditions, $sel);
			$this->createWhere($sel, $options['where_list']);
			if (!empty($options['quicksearch']))
				$sel->where("$column LIKE ?",'%'.$options['quicksearch'].'%');
			$sel->from($this->TableName, array("count(distinct `$column`) as value"));
			//             $rows = $this->fetchAll($sel);
	
	
			//             $this->createWhere($sel, $where_list);
			//             $sel->from($this->TableName, array(new Zend_Db_Expr("$row AS value")));
			//             $sel->distinct();
			//             $sel->limit(1000,0);
			//             $sel->order(array($row));
	
			$rows=$this->Table->fetchAll($sel)->current();
			if ($rows)
				$Results = $rows->value;
	
			//$this->log->debug($Results);
	
			return $Results;
		}
		catch (Exception $ex) {
			throw new Exception($ex, 32001);
		}
	}
	
	
	/**
	 * get distinct values for a row
	 *
	 * @param string row
	 * @param array options
	 * @return int
	 */
	public function getDistinctArray($column, $options) {
		try
		{
			//select  count( distinct `tail_num`) from coding
			//$parameters=array_merge($options,$this->parameters);
	
			//throw new Exception(print_r($column,true));
			$Results = array();
	
			//$this->log->debug($this->TableName);
	
			$sel=$this->Table->select();
			$this->addConditionsToSelect($this->Config->conditions, $sel);
			$this->createWhere($sel, $options['where_list']);
			if (!empty($options['quicksearch']))
				$sel->where("$column LIKE ?",'%'.$options['quicksearch'].'%');
			$sel->distinct();
			$sel->from($this->TableName, array("value"=>$column));
			//             $rows = $this->fetchAll($sel);
	
	
			//             $this->createWhere($sel, $where_list);
			//             $sel->from($this->TableName, array(new Zend_Db_Expr("$row AS value")));
			//             $sel->distinct();
			//             $sel->limit(1000,0);
			//             $sel->order(array($row));
	
			$rows=$this->Table->fetchAll($sel);
			if ($rows) {
				foreach($rows as $row)
					$Results['i'.$row->value] = $options['Mode'];
				//$Results = $rows->toArray();
			}
	
			//$this->log->debug($options['Mode']);
	
			return $Results;
		}
		catch (Exception $ex) {
			throw new Exception($ex, 32001);
		}
	}
	
}