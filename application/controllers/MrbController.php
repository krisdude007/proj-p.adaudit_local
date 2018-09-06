<?php
class MrbController extends Zend_Controller_Action{
    
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
        $mrb = new Application_Model_DbTable_Mrb();
        // $aircraft = new Application_Model_DbTable_Aircraft();

        // $mms = $aircraft->getModelById(5);
        // $aircraftModels = $aircraft->getAircraftById(2);

        // $mrbList = $mrb->getMRBbyATA('24');
        $mrbList = $mrb->getMRB();
        $this->view->mrbList = $mrbList;
    }

    // ajax call from the autocomplete text box
    public function searchAction(){
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        sleep(0.5);
        $searchTerm = $this->getRequest()->getParam('term');

        $mrb = new Application_Model_Shared_Mrb();

        $mrbList = $mrb->getMRBbyMRBnumber($searchTerm);

        $response = array();

        foreach($mrbList as $item){
            $response[] = array(
                                'id' => $item['id'],
                                'label' => $item['mrb_item_number'],
                                'value' => $item['mrb_item_number'],
                                'data' => $item
                                );
        }
// var_dump($response);
        echo json_encode($response);
    }
}