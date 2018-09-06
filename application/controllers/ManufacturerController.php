<?php
class ManufacturerController extends Zend_Controller_Action{

    public $manufacturersTable;

    public function init(){
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->manufacturersTable = new Application_Model_DbTable_Manufacturer();
    }

    public function indexAction()
    {
        $manufacturers = $this->manufacturersTable->getManufacturers('all');
        $this->view->manufacturers = $manufacturers;
    }

    public function createAction()
    {
    }

    public function showAction()
    {
    }

    public function storeAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

         $new_row = $this->manufacturersTable->createRow();
         $new_row->manufacturer_name = $this->_request->getParam('manufacturer_name', null);
         $new_row->manufacturer_type = $this->_request->getParam('manufacturer_type', null);
         $new_row->crea_usr_id       = $this->user['user_id'];
         $new_row->crea_dtm          = date('Y/m/d H:i:s',time());
         $id = $new_row->save();

        $this->_redirect('/manufacturer/index');
    }

    public function editAction()
    {

    }

    public function updateAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $manufacturer_id = $this->_request->getParam('id',0);
        $manufacturer_name = $this->_request->getParam('manufacturer_name',0);
        
        $where = $this->manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer_id);
        try{
            $this->manufacturersTable->update(array(
                'manufacturer_name' => $manufacturer_name,
                'updt_usr_id' => $this->user['user_id'],
                'updt_dtm' => date('Y/m/d H:i:s',time()),
                ), $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = $e->getMessage();
        }
        echo json_encode($row);
    }

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $manufacturer_id = $this->_request->getParam('id',0);
        $where = $this->manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer_id);
        try{
            $this->manufacturersTable->update(array('deleted'=>1), $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = $e->getMessage();
        }
        echo json_encode($row);
    }

}