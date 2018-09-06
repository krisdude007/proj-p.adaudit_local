<?php
class AircraftController extends Zend_Controller_Action{
    
    public $manufacturersTable;
    public $aircraftTable;
    public $engineTable;

    public function init(){
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->manufacturersTable = new Application_Model_DbTable_Manufacturer();       
        $this->aircraftTable = new Application_Model_DbTable_Aircraft();
        $this->engineTable = new Application_Model_DbTable_Engines();
    }

    public function indexAction()
    {
    }

    public function createAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxForm($this->_request->getParam('data', null));
        $formData['user_id'] = $this->user['user_id'];
       try{
            $this->aircraftTable->newAircraft($formData);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }
    
    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $aircraft_id = $this->_request->getParam('id',0);
        
        try{
            $where = $this->aircraftTable->getAdapter()->quoteInto('id = ?', $aircraft_id);
            $this->aircraftTable->update(array('deleted'=>1), $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

}