<?php

class Application_Model_DbTable_AdCustomer extends Zend_Db_Table_Abstract
{

    protected $_name = 'ad_customer';
    protected $_primary = 'ad_customer_id';
	
    protected $_dependentTables = array('Application_Model_DbTable_Ad');
    
    public function findByCustomerID($CustomerID) {
    	$query = $this->select();
    	$query->where('customer_id = ?', $CustomerID);
    	$result = $this->fetchAll($query);
    	return $result;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function fetchAllByCustomerId($CustomerID) {
    	$sql = "";
    }
    
    public function getAd($ad_id) {
    	
    	$query= $this->select();
    	$query->where('ad_id = ?',$ad_id);
    	$results = $this->fetchAll($query)->current();
    	return $results;
    }
    
    public function getAdByAdIdAndCustomerId($adId, $customerId) {
    	
        $query= $this->select();
    	$query->where('ad_id = ?',$adId);
        $query->where('customer_id = ?',$customerId);
    	$results = $this->fetchAll($query);
    	return $results;
    }
    
    public function getAdsByCustomer($CustomerID,$Done=false,$Sort="desc") {
    	
    	$query = $this->select()->setIntegrityCheck(false);
    	$query->from(array('c' => 'ad_customer'),array());
    	$query->join(array('a' => 'ad'), 'a.ad_id = c.ad_id',array());
    	$query->where('c.customer_id = ?', $CustomerID);
    	
    	return($this->fetchAll($query));
    	
    }
    
    public function setLock($ad_id, $user) {
    	$data = array(
    			'ad_status' => $user,
    			'ad_status_dtm' => new Zend_Db_Expr('NOW()')
    			);
    	
    	$where = $this->getAdapter()->quoteInto('ad_id = ?', $ad_id);
    	$this->update($data, $where);
    }
    
    
    public function setStatusWaiting($ad_id) {
    	$data = array(
    			'ad_status' => 'Waiting',
    			'ad_status_dtm' => new Zend_Db_Expr('NOW()-3600')
    	);
    	 
    	$where = $this->getAdapter()->quoteInto('ad_id = ?', $ad_id);
    	$this->update($data, $where);
    }
    
    public function setStatusWaitingbyColumn($column, $ad_id) {
        $data = array(
                "$column" => 'Waiting',
                "$column"."_dtm" => new Zend_Db_Expr('NOW()-3600')
        );
    
        $where = $this->getAdapter()->quoteInto('ad_id = ?', $ad_id);
        $this->update($data, $where);
    }
    
    public function setStatusDone($ad_id) {
    	$data = array(
    			'ad_status' => 'Done',
    			'ad_status_dtm' => new Zend_Db_Expr('NOW()-3600')
    	);
    	
    	$where = $this->getAdapter()->quoteInto('ad_id = ?', $ad_id);
    	$this->update($data, $where);
    	
    	$sql = "UPDATE ad_customer SET ad_status = if(f_isProcessed(1,ad_id)=1, 'Done','Waiting')
    			WHERE (ad_status!='Done') AND (ad_status!='Waiting') 
				AND ((now( ) - ad_status_dtm ) >90)";
    	$this->getAdapter()->query($sql);
    }
    
    public function setStatusDonebyColumn($column, $ad_id) {
        $data = array(
                "$column" => 'Done',
                "$column"."_dtm" => new Zend_Db_Expr('NOW()-3600')
        );
         
        $where = $this->getAdapter()->quoteInto('ad_id = ?', $ad_id);
        $this->update($data, $where);
    }
    
    public function isLocked($ad_id, $user) {
    	
    	$this->updateLocks();
    	
    	$query= $this->select();
    	$query->where('ad_id = ?',$ad_id);
    	$results = $this->fetchAll($query)->current();
    	
		if (isset($results['ad_status']))
	    	if (($results['ad_status']=='Waiting')
	    			||($results['ad_status']=='Done')
	    			||($results['ad_status']==$user)) 
	    		return false;
	    	else
	    		return true;
	    else
	    	return false;
    	
    }
    
    public function isLockedbyColumn($column, $ad_id, $user) {
         
        $this->updateLocks();
         
        $query= $this->select();
        $query->where('ad_id = ?',$ad_id);
        $results = $this->fetchAll($query)->current();
         
        //     	echo "<pre>";
        //     	echo var_dump($results['ad_status']);
        //     	echo "</pre>";
         
        if (isset($results[$column]))
            if (($results[$column]=='Waiting')
                    ||($results[$column]=='Done')
                    ||($results[$column]=='Pending')
                    ||($results[$column]==$user))
            return false;
        else
            return true;
        else
            return false;
         
    }
    
    
    public function updateLocks() {
        
    	$sql = "UPDATE ad_customer SET ad_status ='Waiting' 
    			WHERE (ad_status is null)";
    	$this->getAdapter()->query($sql);
    	
    	$sql = "UPDATE ad_customer SET ad_status = if(f_isProcessed(1,ad_id)=1, 'Done','Waiting')
    			WHERE (ad_status!='Done') AND (ad_status!='Waiting')
				AND ((now( ) - ad_status_dtm ) >90)";
    	$this->getAdapter()->query($sql);
    	
    	
    	// Udpate locks for AD parse
    	$sql = "UPDATE ad_customer SET ad_status_phase2 ='Waiting'
    			WHERE (ad_status_phase2 is null)";
    	$this->getAdapter()->query($sql);
    	 

    	// Release Locks
    	$sql = "UPDATE ad_customer SET ad_status_phase2 = 'Waiting'
    			WHERE (ad_status_phase2!='Done') 
				AND ((now( ) - ad_status_phase2_dtm ) >90)";
    	$this->getAdapter()->query($sql);
    	
    	// Udpate locks for AD interpretation
    	$sql = "UPDATE ad_customer SET ad_interpretation_status ='Waiting'
    			WHERE (ad_interpretation_status is null)";
    	$this->getAdapter()->query($sql);
    	
    	
    	// Release Locks
    	$sql = "UPDATE ad_customer SET ad_interpretation_status = 'Waiting'
    			WHERE (ad_interpretation_status!='Done') and (ad_status_phase2='Done')
				AND ((now( ) - ad_interpretation_status_dtm ) >90)";
    	$this->getAdapter()->query($sql);
    	
    	// Set Pending
    	$sql = "UPDATE ad_customer SET ad_interpretation_status = 'Pending'
    			WHERE  (ad_status_phase2!='Done')
				AND ((now( ) - ad_interpretation_status_dtm ) >90) or (ad_interpretation_status_dtm is null)";
    	$this->getAdapter()->query($sql);
    	
    	
    	// Udpate locks for AD compliance
    	$sql = "UPDATE ad_customer SET ad_compliance_status ='Waiting'
    			WHERE (ad_compliance_status is null)";
    	$this->getAdapter()->query($sql);
    	
    	
    	$sql = "UPDATE ad_customer SET ad_compliance_status_dtm ='0000-00-00 00:00:00'
    			WHERE (ad_compliance_status_dtm is null)";
    	$this->getAdapter()->query($sql);
    	 
    	 
    	// Release Locks
    	$sql = "UPDATE ad_customer SET ad_compliance_status = 'Waiting'
    			WHERE (ad_compliance_status!='Done') and (ad_interpretation_status='Done')
				AND ((now( ) - ad_compliance_status_dtm ) >90)";
    	$this->getAdapter()->query($sql);
    	 
    	// Set Pending
    	$sql = "UPDATE ad_customer SET ad_compliance_status = 'Pending'
    			WHERE  (ad_interpretation_status!='Done')
				AND ((now( ) - ad_compliance_status_dtm ) >90) or (ad_compliance_status_dtm is null)";
    	$this->getAdapter()->query($sql);
    }
    
    public function updateDone() {
        // if audit_end_dt != null Mark audit done by setting ad_status_phse2 to "Done".
        
        // if 
    }
    
    public function updateState($ad_id) {

        $Row=$this->getAd($ad_id);

        if ($Row) {
            // States
            // 1 - Not Started
            // 2 - Parse
            // 3 - Interpretation
            // 4 - Compliance
            // 5 - Superseded
            // 7 - Not Applicable
            // 9 - Compliance With Prod
            
            $State=1;
            $StateDate = "2012-01-01 00:00:00";
            
            if (($Row->audit_end_dt!=null) && ($Row->audit_end_dt!="0000-00-00")) {
                $State = 2;
                $StateDate = $Row->audit_end_dt;
                $Row->ad_status_phase2="Done";
                
            }
            
            if (($Row->audit_end_dt_interpretation!=null) && ($Row->audit_end_dt_interpretation!="0000-00-00")) {
                $State = 3;
                $StateDate = $Row->audit_end_dt_interpretation;
                $Row->ad_interpretation_status="Done";
            }
            
            if (($Row->compliance_audit_start_dt!=null) && ($Row->compliance_audit_start_dt!="0000-00-00")) {
                $State = 4;
                $StateDate = $Row->compliance_audit_end_dt;
                $Row->ad_compliance_status="Done";
            }
          
                
            // Select the one Max row
            $sql="select * from ad_customer_iteration where ad_id = ? order by iteration_num desc limit 0,1";
            $query=$this->getAdapter()->quoteInto($sql, $ad_id);
            $IterationRow = $this->getAdapter()->fetchAll($query);
           // echo "<pre>";
           // print_r($IterationRow);
           // echo "</pre>";
            if (count($IterationRow)>0) {
                if (($IterationRow[0]['audit_end_dt']!=null) && ($IterationRow[0]['audit_end_dt']!= "0000-00-00")) {
                    $State = 4;
                    $StateDate = $IterationRow[0]['audit_end_dt'];
                    $Row->ad_compliance_status="Done";
                }
            }
                
            
            
            if ($Row->compliance_prod==1) {
                $State = 9;
                $StateDate = $Row->ad_status_dtm;
                $Row->ad_status_phase2="Done";
                $Row->ad_interpretation_status="Done";
                $Row->ad_compliance_status="Done";
            }
            
            if (($Row->not_applicable==1)||($Row->not_applicable_phase2)) {
                $State = 7;
                $StateDate = $Row->ad_status_dtm;
                $Row->ad_status_phase2="Done";
                $Row->ad_interpretation_status="Done";
                $Row->ad_compliance_status="Done";
            }
            
            if ($Row->superseded==1) {
                $State = 5;
                $StateDate = $Row->ad_status_dtm;
                $Row->ad_status_phase2="Done";
                $Row->ad_interpretation_status="Done";
                $Row->ad_compliance_status="Done";
            }
            
            $Row->phase2_state_id=$State;
            $Row->phase2_state_dt=$StateDate;
            
            $Row->save();
        }
        else
        {
            echo "<pre>";
            echo "error with ad_id $ad_id\n\n";
            print_r($Row);
            echo "</pre>";
        }
            
    }
    


}

