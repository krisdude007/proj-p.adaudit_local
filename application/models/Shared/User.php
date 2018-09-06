<?php

class Application_Model_Shared_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'user';
    
    /**
     * Use the PhpSlickGrid Rowset class
     * rather than the Zend Rowset class.
     * 
     * @var string
     */
    // protected $_rowsetClass = 'PhpSlickGrid_Db_Table_Rowset';
    
    protected $_referenceMap    = array(
    		'user_app_role' => array(
    				'columns'           => array('user_id'),
    				'refTableClass'     => 'Application_Model_Shared_UserAppRole',
    				'refColumns'        => array('user_id')
    		)
    
    );
    
    protected function _setupDatabaseAdapter()
    {
        // see _initDatabase() in the Bootstrap.php file
        $this->_db = Zend_Registry::get('shared_db');
        parent::_setupDatabaseAdapter();
    }
    
    public function getUserByNameAndPassword($user_nm,$password) {
        $sel = $this->select();
        $sel->where("user_nm = ? ",$user_nm);
        $UserRow=$this->fetchAll($sel)->current();
        if ($UserRow) {
            if ($UserRow->deleted==false){
                if ($UserRow->password==md5($password.$UserRow->salt)) {
                    return $UserRow;
                }
            }
        }
        return false;
    }
    
    public function getUserByNameAndPad($user_nm,$pad) {
        $sel = $this->select();
        $sel->where("user_nm = ? ",$user_nm);
        $sel->where("onetimepad = ? ",$pad);
        $UserRow=$this->fetchAll($sel)->current();
        if ($UserRow) {
            if ($UserRow->deleted==false){
                return $UserRow;
            }
        }
        return false;
    }
    
    public function getActiveUsers(){
    	 $userList = array();
    	 $sel = $this->select();
    	 $sel->where("deleted = ?",0);
    	 $sel->order("user_full_nm");
    	 $userList = $this->fetchAll($sel)->toArray();
    	 $rowCount = 1;
    	 foreach ($userList as $rowArray) {
    	 	$user_id = $rowArray['user_id'];
    	 	foreach ($rowArray as $column => $value) {
    	 		$allUsers[$user_id][$column] = $value;
    	 	}
    	 	++$rowCount;
    	 }
    	 return $allUsers;
    }
    
    public function getActiveCavokUsers(){
        $userList = array();
        $sel = $this->select();
        $sel->where('user_nm like "%cavokgroup.com"');
        $sel->where("deleted = ?",0);
        $sel->order("last_name");
        $userList = $this->fetchAll($sel)->toArray();
        $rowCount = 1;
        foreach ($userList as $rowArray) {
            $user_id = $rowArray['user_id'];
            foreach ($rowArray as $column => $value) {
                $allUsers[$user_id][$column] = $value;
            }
            ++$rowCount;
        }
        return $allUsers;
    }
    
    public function buildEmployeeSelectList(){
        $sel = $this->select();
        $sel->where("deleted = ?",0);
        $sel->where('user_nm like "%cavokgroup.com"');
        $sel->order("last_name");
        $userList = $this->fetchAll($sel)->toArray();
        return $userList;
    }
    
    public function getUserIdList(){
    	$sel = $this->select();
    	$sel->where("deleted = ?",0);
    	$sel->order("user_full_nm");
    	$userList = $this->fetchAll($sel)->toArray();
    	$count = count($userList);
    	for ($i=0;$i<$count;$i++) {
    		$user_id[] = $userList[$i]['user_id'];
    	}
    	return $user_id;
    }
    
    public function getUserFullName($uid){
    	$name = null;
    	$sel = $this->select();
    	$sel->where('user_id = ?',$uid);
    	$row = $this->fetchAll($sel);
    	if ( count($row) == 1 ) {
    		$name = $row[0]['user_full_nm'];
    	}
    	return $name;
    }
    
    public function getUserEmail($uid){
    	$name = null;
    	$sel = $this->select();
    	$sel->where('user_id = ?',$uid);
    	$row = $this->fetchAll($sel);
    	if ( count($row) == 1 ) {
    		$email = $row[0]['user_nm'];
    	}
    	return $email;
    }
    
    public function getUserByID($uid) {
        $user= $this->find($uid)->current();
        if (!($user)) {
            $user=$this->createRow();
            $user['user_nm']='Unknown';
            //$user['user_abbr']='UNK';
            //$user['user_full_nm']='Unknown User';
        }
        $user->password="";
        $user->salt='';
        return $user;
    }
    
    /**
     * Get an array of user_nm with user_id as the key to the 
     * array.  array(user_id=>user_nm,user_id=>user_nm, ... )
     *
     * By: jstormes Sep 23, 2013
     *
     * @param int $app_id
     * @return array
     */
    public function getUsersKeyValBy_app_id($app_id) {
    	
    	$user=array();
    	
       /*******************************************************************
     	* select * from user
    	* join user_app_role on user.user_id = user_app_role.user_id
    	* where user_app_role.app_id = (config->app_id from *.ini)
    	******************************************************************/
    	$sel=$this->select()
    	->setIntegrityCheck(false)
    	->from(array('u' => 'user'))
    	->join(array('uar' => 'user_app_role'),
    			'u.user_id = uar.user_id')
    			->where('uar.app_id = ?',$app_id);
    	
    	$rows = $this->fetchAll($sel);
    	
    	if ($rows) 
    		$users=$rows->toKeyValue("user_nm");

    	return $users;
    	
    }
       
}

