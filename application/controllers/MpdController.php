<?php
class MpdController extends Zend_Controller_Action{
    
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
        $mpd = new Application_Model_DbTable_Mpd();
        $this->view->selectAircraft = $this->aircraftSelect($mpd->getMPDaircraftmodels());
        // $this->view->selectApplAirplane = $mpd->getAirplaneApplicability();
    }

    public function showAction(){
        $mpd = new Application_Model_DbTable_Mpd();
        $aircraft = $this->_request->getParam('aircraft' ,null);
        $revision = $this->_request->getParam('revision',null);
// var_dump($aircraft,$revision);exit;
        $this->view->mpdList = $mpd->getMPD($aircraft,$revision);
    }

    public function highlightsAction(){
        $mpd = new Application_Model_DbTable_Mpd();
        $aircraft = $this->_request->getParam('aircraft' ,null);
        $revision = $this->_request->getParam('revision',null);
        $this->view->highlights = $mpd->getMPDHighlights($aircraft, $revision);
    }

    public function aircraftSelect($aircraft){
        $select = '<select class="form-control" name="aircraft">';
        $select .= '<option value=0>Select...</option>';
        foreach($aircraft as $item){
            $select .= '<option value="'.$item['aircraft'].'">'.$item['aircraft']."</option>";
        }
        $select .= '</select>';
        return $select;
    }

    public function getrevisonhighlightAction(){
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $aircraft = $this->getRequest()->getParam('aircraft');
        $mpd_item = $this->getRequest()->getParam('mpd');
        $revision = $this->getRequest()->getParam('revision');
        $revisionTable = new Application_Model_DbTable_Revision();

        $response = $revisionTable->getItemRevisionHighlight($aircraft, $mpd_item, $revision);
        
        echo json_encode(['message'=>'ok','text'=>$response]);
    }
}