<?php
class TcdsController extends Zend_Controller_Action{

    public $manufacturersTable;
    public $aircraftTable;

    public function init(){
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->manufacturersTable = new Application_Model_DbTable_Manufacturer();       
        $this->aircraftTable = new Application_Model_DbTable_Aircraft();
    }

    public function indexAction()
    {
        $manufacturers = $this->manufacturersTable->getManufacturers();
    }

    public function createAction()
    {
        $this->view->manufacturers = $this->manufacturersTable->getManufacturers('powerplant');
        
    }
}