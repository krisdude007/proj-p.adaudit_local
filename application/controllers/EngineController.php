<?php
class EngineController extends Zend_Controller_Action{

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
        // see index view
    }

    public function createAction()
    {
        $this->view->manufacturers = $this->manufacturersTable->getManufacturers('powerplant');
        
    }

    public function editAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function storeAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $engine_model           = $this->_request->getParam('engine_model',0);
        $engine_manufacturer_id = $this->_request->getParam('engine_manufacturer_id',0);
        $tcds_id                = $this->_request->getParam('tcds_id',0);

        $new_tcds_row                     = $this->engineTable->createRow();
            $new_tcds_row->engine_manufacturer_id = $this->_request->getParam('engine_manufacturer_id',0);
            $new_tcds_row->engine_tcds_id         = $this->_request->getParam('tcds_id',0);
            $new_tcds_row->engine_model           = strtoupper($this->_request->getParam('engine_model',0));
            $new_tcds_row->crea_usr_id            = $this->user['user_id'];
            $new_tcds_row->crea_dtm               = date('Y/m/d H:i:s',time());
        $document_id = $new_tcds_row->save();
       $this->_redirect('/engine/index');
    }

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $engine_id = $this->_request->getParam('id',0);
        
        $where = $this->engineTable->getAdapter()->quoteInto('engine_id = ?', $engine_id);
        try{
            $this->engineTable->update(array('deleted'=>1), $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }
}