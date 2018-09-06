<?php

class VersionsController extends Zend_Controller_Action{
    
    public function init()
     {
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
    }
    
    // Show the form to add a new version of the document
    // This form POSTS to /import/postversion
    public function addAction()
    {
        $document_id = $this->_request->getParam('id', null);
        $documentsTable = new Application_Model_DbTable_Documents();
        $this->view->document = $documentsTable->getWithVersions($document_id);
    }

    // View the version data
    public function viewAction()
    {   
        $document_id = $this->_request->getParam('id', null);
        $document_version_id = $this->_request->getParam('vid', null);

        $documentTable = new Application_Model_DbTable_Documents();
        $document = $documentTable->get($document_id);
        $this->view->document = $document;

        $manufacturerTable = new Application_Model_DbTable_Manufacturer();
        $manufacturer = $manufacturerTable->getName($document['manufacturer_id']);
 
        $versionTable = new Application_Model_DbTable_Documentversions();
        $version = $versionTable->get($document_version_id);
        $this->view->version = $version;
        
        $sourceTable = new Application_Model_DbTable_Sourcedocuments();
        $this->view->sources = $sourceTable->getFileByUid($version['uid']);

        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $tableColumns = $storageTable->info(Zend_Db_Table_Abstract::COLS);
        $this->view->table_columns = $this->buildTableColumns($tableColumns, array('document_id','document_version_id'));

        // $records = $storageTable->getByDocId($document_id, $document_version_id);
        // $records['number'] = count($records);
        // $this->view->records = $records;

        // $intervalMethodTable = new Application_Model_DbTable_Intervalmethod();
        // $interval_methods = $intervalMethodTable->getMethods();
        // $this->view->interval_methods = $interval_methods;
        
        $this->view->document_id = $document_id;
        $this->view->document_version_id = $document_version_id;
    }  

    // Show the form to edit a version
    public function editAction()
    {
        $document_version_id = $this->_request->getParam('id', null);
    }

    // Store the edited version information
    public function updateAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_version_id = $this->_request->getParam('vid', null);
        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxForm($this->_request->getParam('data', null));
        $formData['updt_usr_id'] = $this->user['user_id'];
        $formData['updt_dtm'] = date('Y/m/d H:i:s',time());
         try{
            $documentVersionsTable = new Application_Model_DbTable_Documentversions();
            $documentVersionsTable->updateVersion($document_version_id, $formData);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
            $row['text'] = $e;
        }
        echo json_encode($row);
    }

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_id         = $this->_request->getParam('did', null);
        $document_version_id = $this->_request->getParam('vid', null);
        $row_id              = $this->_request->getParam('rid', null);

        try{
            $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
            $storageTable->deleteRecordsByDocumentVersionID($document_version_id);
            $documentVersionsTable = new Application_Model_DbTable_Documentversions();
            $documentVersionsTable->deleteDocumentVersions($document_version_id);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
            $row['text'] = $e;
        }
        echo json_encode($row);
    }

    // Show the form to edit a row of data in the source
    public function editrowAction()
    {
        $document_id         = $this->_request->getParam('id', null);
        $document_version_id = $this->_request->getParam('vid', null);
        $row_id              = $this->_request->getParam('rid', null);

        $documentTable = new Application_Model_DbTable_Documents();
        $this->view->document = $documentTable->get($document_id);
        $versionTable = new Application_Model_DbTable_Documentversions();
        $this->view->version = $versionTable->get($document_version_id);

        $this->view->document_id = $document_id;
        $this->view->document_version_id = $document_version_id;
        $this->view->row_id = $row_id;

        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $row = $storageTable->getRow($document_id, $row_id);
        $ignore_keys = array('id','document_id','document_version_id','crea_usr_id','crea_dtm','updt_usr_id','updt_dtm','deleted');
        $this->view->row = array_diff_key($row, array_flip($ignore_keys));
       
        $messages['success'] = $this->_helper->FlashMessenger->getMessages('Success');
        $messages['error'] = $this->_helper->FlashMessenger->getMessages('Error');
        $this->view->messages = $messages;
    }

    // Store the edited row data in the storage table.
    public function updaterowAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_id         = $this->_request->getParam('document_id', null);
        $document_version_id = $this->_request->getParam('document_version_id', null);
        $row_id              = $this->_request->getParam('id', null);

        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $row = $storageTable->getRow($document_id, $row_id);
        $old_data = json_encode($row);
        $new_data = json_encode($this->getRequest()->getPost());

        $where = $storageTable->getAdapter()->quoteInto('id = ?', $row_id);

        try{
            $storageTable->update($this->getRequest()->getPost(), $where);
            $result = true;
        }
        catch (Exception $e){
            $result = false;
        }
        if($result){
            //********* save the change to the history table
            $this->_helper->FlashMessenger->addMessage('Data Saved','Success');
        }else{
            $this->_helper->FlashMessenger->addMessage('Data Not Saved','Error');
        }

        $this->_redirect("/versions/editrow/id/$document_id/vid/$document_version_id/rid/$row_id");
    }

    public function deleterowAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_id         = $this->_request->getParam('id', null);
        $document_version_id = $this->_request->getParam('vid', null);
        $row_id              = $this->_request->getParam('rid', null);
        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $row = $storageTable->getRow($document_id, $row_id);
        $where = $storageTable->getAdapter()->quoteInto('id = ?', $row_id);
        try{
            $storageTable->update(array('deleted'=>1), $where);
            $result = true;
        }
        catch (Exception $e){
            $result = false;
        }
        if($result){
            $this->_helper->FlashMessenger->addMessage('Data Saved','Success');
        }else{
            $this->_helper->FlashMessenger->addMessage('Data Not Saved','Error');
        }
        $this->_redirect("/document/view/id/$document_id/vid/$document_version_id");
    } 

    public function getintervalsAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        $document_id      = $this->_request->getParam('document_id', null);
        $document_item_id = $this->_request->getParam('document_item_id', null);
        $result = [];
        try{
            $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
            $intervalsTable = new Application_Model_DbTable_Intervals();
            $intervals = $intervalsTable->getByDocItemId($document_id, $document_item_id);
            $result['message'] = 'ok';
            $result['data'] = json_encode($intervals);
        }
        catch (Exception $e){
            $result['message'] = 'fail';
            $result['text'] = $e;
        }
        echo json_encode($result);
    }

    private function buildTableColumns($tableColumns, $hiddenColumns = array())
    {
        $columns = $this->removeHiddenColumns($tableColumns, $hiddenColumns);
        $tempArray = array();
        foreach($columns as $column){
            if($column == 'change_bar'){
                $tempArray[] = array(
                    'field'     => $column,
                    'title'     => ucwords(str_replace('_', ' ', $column)),
                    'sortable'  => true,
                    'cellStyle' => 'changeBarCellStyle',
                    'class'     => 'change-bar',
                    'formatter' => 'nl2brFormatter'
                );

            }else{
                $tempArray[] = array(
                    'field'     => $column,
                    'title'     => ucwords(str_replace('_', ' ', $column)),
                    'sortable'  => true,
                    'formatter' => 'nl2brFormatter'
                );
            }
        }
        $tempArray[] = array(
                'field' => 'tools',
                'title' => '',
                'align' => 'center',
                'sortable'  => false,
                'events' => 'toolEvent',
                'formatter' => 'toolFormatter',
                'switchable' => false
                );
        return json_encode($tempArray);
    }
        /**
     * Remove certain DB table required columns from the list the user sees
     * @param  array $tableColumns  The array of columns in the table
     * @param  array $hiddenColumns The columns to remove
     * @return array $tableColumns  The array with hidden columns removed
     */
    private function removeHiddenColumns($tableColumns, $hiddenColumns = array()) 
    {
        $hidden = array_merge($hiddenColumns, array('crea_usr_id','crea_dtm','updt_usr_id','updt_dtm','deleted'));
        foreach($tableColumns as $key=>$value) {
            if (in_array($key,$hidden))
                unset($tableColumns[$key]);
            else
            if (in_array($value,$hidden))
                unset($tableColumns[$key]);
        }
        return $tableColumns;
    }
}