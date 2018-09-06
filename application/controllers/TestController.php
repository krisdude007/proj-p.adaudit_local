<?php
class TestController extends Zend_Controller_Action{
    public $project_id = 0;

    public function init(){
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->project_id = $this->_request->getParam('project_id',0);
        $this->view->project_id = $this->project_id;
    }

    public function indexAction(){
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $aircraftTable = new Application_Model_DbTable_Aircraftreference();
        $aircraft = $aircraftTable->getManufacturers();
        var_dump($aircraft);
        $aircraft = $aircraftTable->getAircraft();
        var_dump($aircraft);
    }

}