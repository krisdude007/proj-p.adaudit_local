<?php
class DashboardController extends Zend_Controller_Action{
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
        $mpdTable = new Application_Model_DbTable_Mpd();
        $aircraft = $mpdTable->getMPDaircraftmodels();
        $mpds = array();
        foreach ($aircraft as $key => $value) {
            $mpds[$value['aircraft']] = $mpdTable->getAvailableRevisions($value['aircraft']);
        }
        $this->view->mpds = $mpds;
    }

}