<?php
class DocumentController extends Zend_Controller_Action{

    public function init()
     {
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
    }

    public function indexAction()
    {
        $documentsTable = new Application_Model_DbTable_Documents();
        $this->view->documents = $documentsTable->getAllWithVersions();
    }

    public function viewAction()
    {

    }

    public function downloadAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_id = $this->_request->getParam('id', null);
        $documentsTable = new Application_Model_DbTable_Sourcedocuments();
        $document = $documentsTable->getFileById($document_id);

        $thisFile = $document['file_storage_nm'];
        $file_name = '../files/'.$thisFile[0].'/'.$thisFile;

        if (file_exists($file_name)) {

            // first, get MIME information from the file
            $finfo = finfo_open(FILEINFO_MIME_TYPE); 
            $mime =  finfo_file($finfo, $file_name);
            finfo_close($finfo);

            // send header information to browser
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename="'.basename( $document['name'] .'"'));
            header('Content-Length: ' . filesize($file_name));
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            //stream file
            ob_get_clean();
            echo file_get_contents($file_name);
            ob_end_flush();

        }else{
            throw new Zend_Controller_Action_Exception('File not found', 404);
            // echo 'ERROR: File not found';
            // echo "\n\n";
            // echo $file_name;
        }

    }

    public function editAction()
    {
        $document_id = $this->_request->getParam('id', null);
    }

    public function updateAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $document_id = $this->_request->getParam('did', null);
        // $data = $this->_request->getParam('data', null);
        // $temp = array();
        // foreach ($data as $key => $value) {
        //     $temp[$value['name']]=$value['value'];
        // }
        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxForm($this->_request->getParam('data', null));
        try{
            $documentsTable = new Application_Model_DbTable_Documents();
            $documentsTable->updateDocument($document_id, $formData);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
            $row['text'] = $e;
        }
        echo json_encode($row);
    }

    public function updaterecordAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        // var_dump($_POST);

        $row['message'] = 'ok';
            
        echo json_encode($row);
    }



    // GET - show form to create a new document
    public function createAction()
    {

    }
    // POST - store the new document in the DB
    public function storeAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // $data = $this->_request->getParam('data', null);
        // $temp = array();
        // foreach ($data as $key => $value) {
        //     $temp[$value['name']] = $value['value'];
        // }
        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxForm($this->_request->getParam('data', null));
        $formData['crea_usr_id'] = $this->user['user_id'];
        $formData['crea_dtm'] = date('Y/m/d H:i:s',time());

        try{
        $documentsTable = new Application_Model_DbTable_Documents();
        $documentsTable->store($formData);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'fail';
            $row['text'] = $e;
        }
        echo json_encode($row);
    }

    // Delete a document and its versions
    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_id = $this->_request->getParam('did', null);

        try{
            $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
            $storageTable->deleteRecordsByDocumentID($document_id);
            $documentVersionsTable = new Application_Model_DbTable_Documentversions();
            $documentVersionsTable->deleteDocumentVersions($document_id);
            $documentsTable = new Application_Model_DbTable_Documents();
            $documentsTable->deleteDocument($document_id);
            $row['message'] = 'ok';

            $changeTable = Application_Model_DbTable_Changehistory();
            $changeTable->changed(array(
                'who'=>'User: '.$this->user['user_full_nm']. ' ID: '.$this->user['user_id'],
                'what'=>'Document ID:'.$document_id . ' Deleted',
                'when'=> date('Y/m/d H:i:s',time()),
                'table'=>get_class($storageTable),
                'user_id'=>$this->user['user_id']
                )
            );
        }
        catch (Exception $e){
            $row['message'] = 'ERR: ' . __METHOD__ . ' -- ' .$e;
            $row['text'] = $e;
        }
        echo json_encode($row);
    }

    // Delete a record from a document
    public function deleterecordAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $document_id = $this->_request->getParam('did', null);
        $document_version_id = $this->_request->getParam('document_version_id', null);
        $record_id = $this->_request->getParam('rid', null);

        try{
            $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
            $storageTable->deleteRecord($record_id);
            $changeTable = new Application_Model_DbTable_Changehistory();
            $changeTable->changed(array(
                'who'     => 'User: '.$this->user['user_full_nm']. ' ID: '. $this->user['user_id'],
                'what'    => 'Document ID:'.$document_id . ' Record ID:'.$record_id . ' Deleted',
                'when'    => date('Y/m/d H:i:s',time()),
                'table'   => get_class($storageTable),
                'user_id' => $this->user['user_id']
                )
            );
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'ERR: ' . __METHOD__ . ' -- ' .$e;
            $row['text'] = $e;
        }
        echo json_encode($row);
    }
}