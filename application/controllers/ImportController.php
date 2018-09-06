<?php

class ImportController extends Zend_Controller_Action{

    private $Session;
    private $utility;

    public function init()
    {
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');

        $this->session = new Zend_Session_Namespace('DocLibrary');
        require_once 'PHPExcel.php';
        require_once 'PHPExcel/IOFactory.php';
        require_once 'ICAparsers/Airbus.php';
        require_once 'ICAparsers/Boeing.php';
        require_once 'ICAparsers/Embraer.php';
        require_once 'Utility.php';

        $this->utility = new Utility();
    }

    /**
     * Import the spreadsheet for the new version
     * Posted from: /versions/add/id/23
     */
    public function versionAction()
    {
        $start_time = time();
        $columnNameRow  =  1;

        $document_id          = $this->_request->getParam('document_id', null);
        $document_version_id  = $this->_request->getParam('version_id', null);
        $file_revision_number = $this->_request->getParam('revision_number', null);
        $file_revision_date   = $this->_request->getParam('revision_date', null);
        $file_comment         = $this->_request->getParam('comment', null);

        $documentVersionTable = new Application_Model_DbTable_Documentversions();
        $sourceDocumentTable  = new Application_Model_DbTable_Sourcedocuments();
        $intervalTable        = new Application_Model_DbTable_Intervals();
        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);

        $importFile = new Zend_File_Transfer_Adapter_Http();
        // it's a valid file so start to use it.
        $importFile->receive();
        $fileInfo = $importFile->getFileInfo();

        if(is_null($document_version_id)){
            $document_version = $documentVersionTable->newVersion($document_id, array(
                'user_id'      => $this->user['user_id'],
                'revision_dt'  => $file_revision_date,
                'revision_num' => $file_revision_number,
                'document_id'  => $document_id,
                'comment'      => $file_comment
                ));
            $document_version_id = $document_version['id'];
        }else{
            $document_version = $documentVersionTable->get($document_version_id);
        }
        $newSourceDocument = $sourceDocumentTable->newDocument($fileInfo, $document_version);

        $fileStored = $this->storeFile($fileInfo, $newSourceDocument);
        $source_document_id = $fileStored['file']['id'];
        $source_document_uid = $fileStored['file']['uid'];

        $sourceDocument = $sourceDocumentTable->getFileById($source_document_id);

        $sourceFile = '../files/'.$sourceDocument['file_storage_nm'][0].'/'.$sourceDocument['file_storage_nm'];

        // ** load the worksheet
        $inputFileType = PHPExcel_IOFactory::identify($sourceFile);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        // **  Get the list of worksheet names and then select the one that we want to load  **/
        $worksheetList = $objReader->listWorksheetNames($sourceFile);
        $sheetname = $worksheetList[0];
        // **  Tell the Excel reader  which worksheet we want to load  **/
        $objReader->setLoadSheetsOnly($sheetname);
        // **  Load $sourceFile into a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($sourceFile);
        $sourceColumns = $objPHPExcel->getActiveSheet()->rangeToArray('A' . $columnNameRow . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $columnNameRow);

        $document_version['columns'] = json_encode($sourceColumns[0]);
        $result = $documentVersionTable->updateVersion($document_version['id'], $document_version);
        $sourceData = $objPHPExcel->getActiveSheet();


        // transfer the spreadsheet data into the storage table
        $rowCount = 0;
        foreach($sourceData->getRowIterator() as $row){
            // if($rowCount < $columnNameRow) $rowCount++; continue;
            $new_row = $storageTable->createRow();
            $new_row->document_id = $document_id;
            $new_row->document_version_id = $document_version_id;
            $colCount = 0;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = array();
            foreach ($cellIterator as $cell) {
                $columnName = $this->utility->getColumnLetter($colCount, false);
                $new_row->$columnName = $cell->getValue() ?: null;
                $rowData[$sourceColumns[0][$colCount]] = $cell->getValue();
                $colCount++;
            }

            $new_row->sheet_name = $sheetname;
            $row_id = $new_row->save();

            $rowCount++;
        }

        // Now go through the saved rows and insert the intervals into their table
        $this->saveIntervals($document_id, $document_version_id);

        // Call it a day
        $end_time = time();
        $history = array(
            'start_time' => $start_time,
            'end_time'   => $end_time,
            'rows_saved' => $objPHPExcel->getActiveSheet()->getHighestRow()
            );

        $this->saveTransactionHistory($source_document_id, $history, $sheetname);
        $this->_redirect('/document/index');
    }

    /**
     * Does some setup and pulls the records from the storage table
     *
     * @param      int  $document_id          The document identifier
     * @param      int  $document_version_id  The document version identifier
     *
     * @return     bool  always true
     */
    public function saveIntervals($document_id, $document_version_id)
    {
        $documentVersionTable = new Application_Model_DbTable_Documentversions();
        $sourceDocumentTable  = new Application_Model_DbTable_Sourcedocuments();

        $intervalTable = new Application_Model_DbTable_Intervals();
        $intervalTable->clearTable($document_id);

        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $version = $documentVersionTable->get($document_version_id);
        $rows = $storageTable->getByDocIdNoIntervals($document_id, $document_version_id);

        $versionColumnsArray = json_decode($version['columns']);

        // These are the column names where the intervals data is stored
        $intervalTypes = [
            'primary_repeat_unit',
            'primary_repeat_value',
            'secondary_repeat_unit',
            'secondary_repeat_value',
            'tertiary_repeat_unit',
            'tertiary_repeat_value',
            'primary_threshold_unit',
            'primary_threshold_value',
            'secondary_threshold_unit',
            'secondary_threshold_value',
            'tertiary_threshold_unit',
            'tertiary_threshold_value'];

        // Map the named columns to the lettered columns
        $columnsArray = array();
        foreach ($intervalTypes as $intervalKey => $intervalValue) {
            foreach($versionColumnsArray as $key => $value){
                if($intervalValue == $value){
                    $columnName = $this->utility->getColumnLetter($key, false);
                    $columnsArray[$value] = $columnName;
                }
            }
        }

        // Loop through each row of saved data and save the intervals into their table
        foreach($rows as $row){
            $result = $this->saveRowIntervals($columnsArray, $row);
        }
        // $this->utility->log( array('name'=>'intervals '.date("d-m-Y"), 'message' => 'Test message'));
        return true;
    }

    /**
     * Extracts the intervals from the passed in row. Stores in the intervals table.
     *
     * @param      array  $columns  The columns
     * @param      array  $row      The row
     *
     * @return     bool  always true
     */
    public function saveRowIntervals($columns, $row)
    {
        $intervalTable = new Application_Model_DbTable_Intervals();

        $saveData = array();

        $saveData['document_id']      = $row['document_id'];
        $saveData['document_item_id'] = $row['id'];
        $saveData['crea_usr_id'] = $this->user['user_id'];
        $saveData['crea_dtm'] = date('Y-m-d H:i:s',time());

        if(!is_null($row[$columns['primary_repeat_unit']]) && !is_null($row[$columns['primary_repeat_value']])){
            $saveData['interval_type'] = $row[$columns['primary_repeat_unit']];
            $saveData['interval_value'] = $row[$columns['primary_repeat_value']];
            $saveData['interval_method'] = 'repeat';
            $row_id = $intervalTable->insert($saveData);
        }
        if(!is_null($row[$columns['secondary_repeat_unit']]) && !is_null($row[$columns['secondary_repeat_value']])){
            $saveData['interval_type'] = $row[$columns['secondary_repeat_unit']];
            $saveData['interval_value'] = $row[$columns['secondary_repeat_value']];
            $saveData['interval_method'] = 'repeat';
            $row_id = $intervalTable->insert($saveData);
        }
        if(!is_null($row[$columns['tertiary_repeat_unit']]) && !is_null($row[$columns['tertiary_repeat_value']])){
            $saveData['interval_type'] = $row[$columns['tertiary_repeat_unit']];
            $saveData['interval_value'] = $row[$columns['tertiary_repeat_value']];
            $saveData['interval_method'] = 'repeat';
            $row_id = $intervalTable->insert($saveData);
        }
        if(!is_null($row[$columns['primary_threshold_unit']]) && !is_null($row[$columns['primary_threshold_value']])){
            $saveData['interval_type'] = $row[$columns['primary_threshold_unit']];
            $saveData['interval_value'] = $row[$columns['primary_threshold_value']];
            $saveData['interval_method'] = 'threshold';
            $row_id = $intervalTable->insert($saveData);
        }
        if(!is_null($row[$columns['secondary_threshold_unit']]) && !is_null($row[$columns['secondary_threshold_value']])){
            $saveData['interval_type'] = $row[$columns['secondary_threshold_unit']];
            $saveData['interval_value'] = $row[$columns['secondary_threshold_value']];
            $saveData['interval_method'] = 'threshold';
            $row_id = $intervalTable->insert($saveData);
        }
        if(!is_null($row[$columns['tertiary_threshold_unit']]) && !is_null($row[$columns['tertiary_threshold_value']])){
            $saveData['interval_type'] = $row[$columns['tertiary_threshold_unit']];
            $saveData['interval_value'] = $row[$columns['tertiary_threshold_value']];
            $saveData['interval_method'] = 'threshold';
            $row_id = $intervalTable->insert($saveData);
        }
        return true;
    }

    /**
     * Stores a file.
     *
     * @param      array  $fileInfo    The file information
     * @param      array  $sourceFile  The source file
     *
     * @return     array  Info about the transfer
     */
    public function storeFile($fileInfo, $sourceFile=null)
    {

        $file = isset($fileInfo['importfile']['tmp_name']) ? $fileInfo['importfile']['tmp_name'] : $fileInfo['tmp_name'];
        $source = $file;
        $storageName = $sourceFile['file_storage_nm'];
        $destination = '../files/'. $storageName[0] .'/'. $storageName;

        $copied = copy($source, $destination);
        $deleted = unlink($source);
        return array(
            'result'=> $copied&&$deleted,
            'copied'=>$copied,
            'deleted'=>$deleted,
            'file'=>$sourceFile
        );
    }


    /**
      * Let the user select the type of document, manfacturer and aircraft, and upload the file
      * @return view import/getimport.phtml
    **/ 
    public function getimportAction()
    {
        // this just displays the form
    }

    // show the form to add a document to an existing version
    public function getaddtoversionAction()
    {
        $document_id          = $this->_request->getParam('id', null);
        $document_version_id = $this->_request->getParam('vid', null);
        $documentsTable = new Application_Model_DbTable_Documents();
        $document = $documentsTable->get($document_id);

        $documentVersionTable = new Application_Model_DbTable_Documentversions();
        $document_version = $documentVersionTable->get($document_version_id);
        $this->view->document = array('document' => $document, 'version' => $document_version);
    }

    // add a version to a document
    // This is a POST from /versions/add/id/XX
    // Loads the column mapping screen
    //      /views/scripts/import/postversion
    //      /views/scripts/import/mapcolumns
    //
    public function postversionAction()
    {
        // $this->_helper->layout()->disableLayout(true);
        // $this->_helper->viewRenderer->setNoRender(true);

        $document_id          = $this->_request->getParam('document_id', null);
        $document_version_id  = $this->_request->getParam('version_id', null);
        $file_revision_number = $this->_request->getParam('revision_number', null);
        $file_revision_date   = $this->_request->getParam('revision_date', null);
        $file_comment         = $this->_request->getParam('comment', null);

        $importFile = new Zend_File_Transfer_Adapter_Http();

        // $importFile->addValidator('MimeType',false, 'application/vnd.ms-excel');
       //  // $importFile->addValidator('MimeType',false, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
       // // ->addValidator('Size', false, array('max' => '512kB'))
       //  if( ! $importFile->isValid() ){
       //      $this->view->error = array('state'=>'danger','message'=>'File type must be Excel');
       //      // die('wrong file type');
       //      $this->_redirect('/import/getimport');
       //  }

       //  // it's a valid file so start to use it.
        $importFile->receive();
        $fileInfo = $importFile->getFileInfo();

        // $adapter = new Zend_File_Transfer_Adapter_Http();
        // $adapter->addValidator('Extension', false,
                       // 'xlsx');
        //var_dump($adapter->getFileInfo());
        // if (!$adapter->isValid())
        // {
        //     echo 'not valid';
        //     // return 'ERROR';
        // } else {
        //     echo 'valid';
        // }
        // exit;

        $documentVersionTable = new Application_Model_DbTable_Documentversions();
        if(is_null($document_version_id)){
            $document_version = $documentVersionTable->newVersion($document_id, array(
                'user_id'      => $this->user['user_id'],
                'revision_dt'  => $file_revision_date,
                'revision_num' => $file_revision_number,
                'document_id'  => $document_id,
                'comment'      => $file_comment
                ));
            $document_version_id = $document_version['id'];
        }else{
            $document_version = $documentVersionTable->get($document_version_id);
        }

        $sourceDocument = new Application_Model_DbTable_Sourcedocuments();
        $newSourceDocument = $sourceDocument->newDocument($fileInfo, $document_version);

        $fileStored = $this->storeFile($fileInfo, $newSourceDocument);

        $worksheetNames = $this->getWorksheets('../files/'. $fileStored['file']['file_storage_nm'][0] .'/'. $fileStored['file']['file_storage_nm']);
        $this->view->worksheetNames      = $worksheetNames;
        $this->view->document_id         = $document_id;
        $this->view->document_version_id = $document_version_id;
        $this->view->source_document_id = $newSourceDocument['id'];
    }

    // return the worksheets contained in $filePath
    private function getWorksheets($filePath)
    {
        // get the worksheet names
        try {
            $inputFileName =  $filePath;
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $worksheetNames = $objReader->listWorksheetNames($inputFileName);
            return $worksheetNames;
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
    }

    // post the data to add a document to an existing version
    public function postadddocumenttoversionAction()
    {

    }

    /**
     * Let the user select the worksheet and map worksheet columns to DB table columns
     * @return view import/postimport.phtml
     */
    public function postimportAction()
    {
        $importFile = new Zend_File_Transfer_Adapter_Http();
        $importFile->addValidator('MimeType',false, 'application/vnd.ms-excel');
       // ->addValidator('Size', false, array('max' => '512kB'))
        if( ! $importFile->isValid() ){
            $this->view->error = array('state'=>'danger','message'=>'File type must be Excel');
            // die('wrong file type');
            $this->_redirect('/import/getimport');
        }

        // it's a valid file so start to use it.
        $importFile->receive();
        $fileInfo = $importFile->getFileInfo();
        $tmp_name = $fileInfo['importfile']['tmp_name'];

        /*************** Need file storage name!!!!!!!!!!!!!!!!!!! **************/
        $fileStored = $this->storeFile($fileInfo['importfile']);

        if(! $fileStored['result']){
            // something went wrong during the file saving maneuver
            if(! $fileStored['copied']){
                // file wasn't copied to destination
            }
            if(! $fileStored['deleted']){
                // file wasn't deleted from /var/tmp
            }
        }

        $source_document_id = $fileStored['file']['id'];
        $source_document_uid = $fileStored['file']['uid'];

        $document_id = $this->_request->getParam('document_id', null);
        $documentTypeTable = new Application_Model_DbTable_Documenttype();

        // document_id wasn't passed in so this came from import/getimport
        if(is_null($document_id)){
            $model_id = $this->_request->getParam('model', null);
            $document_type = $documentTypeTable->get($this->_request->getParam('type', null));

            $manufacturerTable = new Application_Model_DbTable_Manufacturer();
            $manfacturer = $manufacturerTable->getName( $this->_request->getParam('manufacturer', null) );

            $document = new Application_Model_DbTable_Documents();
                $new_document_row                     = $document->createRow();
                $new_document_row->document_type_id   = $this->_request->getParam('type', null);
                $new_document_row->manufacturer_id    = $this->_request->getParam('manufacturer', null);
                $new_document_row->revision_num       = $this->_request->getParam('revision_number', null);
                $new_document_row->revision_dt        = $this->_request->getParam('revision_date', null);
                $new_document_row->aircraft_model_id  = $this->_request->getParam('model', null);
                $new_document_row->source_document_id = $source_document_id;
                $new_document_row->crea_usr_id        = $this->user['user_id'];
                $new_document_row->crea_dtm           = date('Y/m/d H:i:s',time());
            $document_id = $new_document_row->save();
            $version_id = null;

        }else{
            // document_id was passed in so this came from versions/add
            $documentTable = new Application_Model_DbTable_Documents();
            $document = $documentTable->get($document_id);

            $version = new Application_Model_DbTable_Documentversions();
                $new_version_row                     = $version->createRow();
                $new_version_row->document_id        = $document_id;
                $new_version_row->uid                = sha1(uniqid(mt_rand(), true));
                $new_version_row->revision_num       = $this->_request->getParam('revision_number', null);
                $new_version_row->revision_dt        = $this->_request->getParam('revision_date', null);
                $new_version_row->comment            = $this->_request->getParam('comment', null);
                $new_version_row->source_document_id = $source_document_id;
                $new_version_row->crea_usr_id        = $this->user['user_id'];
                $new_version_row->crea_dtm           = date('Y/m/d H:i:s',time());;
            $version_id = $new_version_row->save();

            $document_type = $documentTypeTable->get($document['document_type_id']);
            $manufacturer = $document['manufacturer_id'];
            $model_id = $document['aircraft_model_id'];
        }

        // $importTransaction = new Application_Model_DbTable_Importtransaction();
        //     $import_transaction_row                      = $importTransaction->createRow();
        //     $import_transaction_row->document_id         = $document_id;
        //     $import_transaction_row->document_version_id = $version_id ? $version_id : null;
        //     $import_transaction_row->file_name           = $fileInfo['importfile']['name'];
        //     $import_transaction_row->file_type           = $fileInfo['importfile']['type'];
        //     $import_transaction_row->tmp_name            = $fileInfo['importfile']['tmp_name'];
        //     $import_transaction_row->size                = $fileInfo['importfile']['size'];
        //     $import_transaction_row->document_type       = $document_type['type'];
        //     $import_transaction_row->manufacturer        = isset($manufacturer) ? $manufacturer : null;
        //     $import_transaction_row->model               = $model_id;
        //     $import_transaction_row->updt_usr_id         = $this->user['user_id'];
        //     $import_transaction_row->status              = 'Started';
        //     $importTransaction_id                        = $import_transaction_row->save();

        // get the worksheet names
        try {
            $inputFileName = '../files/'. $fileStored['file']['file_storage_nm'][0] .'/'. $fileStored['file']['file_storage_nm'];
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $worksheetNames = $objReader->listWorksheetNames($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $documentData['document_type']       = $this->_request->getParam('type', null);
        $documentData['manufacturer']        = $this->_request->getParam('manufacturer', null);
        $documentData['model']               = $this->_request->getParam('model', null);
        $documentData['source_document_id']  = $source_document_id;
        $documentData['source_document_uid'] = $source_document_uid;

        $this->view->worksheetNames = $worksheetNames;
        $this->view->documentData = $documentData;
    }

    /**
     * The user has mapped the data columns, now do the import
     * @return [type] [description]
     */
    public function postimportworksheetAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $start_time = time();

        $document_id         = $this->_request->getParam('document_id', null);
        $source_document_id  = $this->_request->getParam('source_document_id', null);
        $document_version_id = $this->_request->getParam('document_version_id', null);
        $mappedColumns       = $this->_request->getParam('select_mapping', null);
        $worksheetIndex      = $this->_request->getParam('worksheet_index', null);

        $sourceDocumentTable = new Application_Model_DbTable_Sourcedocuments();
        $sourceDocument = $sourceDocumentTable->getFileById($source_document_id);

        $sourceFile = '../files/'.$sourceDocument['file_storage_nm'][0].'/'.$sourceDocument['file_storage_nm'];

        // This defaults to row 1. Currently user selection of this value is not implemented in any of the forms or other code
        $columnNameRow  =  1; //$importTransaction['data_start_row'] > 0 ? $importTransaction['data_start_row'] : 1;

        // load the worksheet
        $inputFileType = PHPExcel_IOFactory::identify($sourceFile);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        // **  Get the list of worksheet names and then select the one that we want to load  **/
        $worksheetList = $objReader->listWorksheetNames($sourceFile);
        $sheetname = $worksheetList[$worksheetIndex];
        /**  Tell the Excel reader  which worksheet we want to load  **/
        $objReader->setLoadSheetsOnly($sheetname);
        /**  Load $sourceFile into a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($sourceFile);
        $sourceColumns = $objPHPExcel->getActiveSheet()->rangeToArray('A' . $columnNameRow . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $columnNameRow);

        // check to make sure the number of spreadsheet columns equals the number of user-mapped columns
        $this->checkColumnCountIsEqual($mappedColumns, $sourceColumns[0]);

        $sourceData = $objPHPExcel->getActiveSheet();

        $intervalTable = new Application_Model_DbTable_Intervals();

        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);

        // transfer the spreadsheet data into the storage table
        foreach($sourceData->getRowIterator() as $row){
            $new_row = $storageTable->createRow();
            $new_row->document_id = $document_id;
            $new_row->document_version_id = $document_version_id;
            $colCount = 0;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                if($mappedColumns[$colCount] == 'ignore'){
                    $colCount++;
                    continue;
                }
                $new_row->$mappedColumns[$colCount] = $cell->getValue() ?: null;
                $colCount++;
            }
            $new_row->sheet_name = $sheetname;
            $row_id = $new_row->save();


            //************* This will be the place to change how the data is put into the intervals table
            // $this->parseSavedRow($document_id, $row_id);
            // $this->saveRowIntervals($document_id, $row_id);
            //
            // CALL ANOTHER ACTION AFTER THE SPREADSHEET HAS BEEN SAVED
        }

        // Call it a day
        $end_time = time();
        $history = array(
            'start_time' => $start_time,
            'end_time'   => $end_time,
            'rows_saved' => $objPHPExcel->getActiveSheet()->getHighestRow()
            );

        $this->saveTransactionHistory($source_document_id, $history, $sheetname);
        $this->_redirect('/document/index');
    }


public function parsetestAction()
{

    // call like so: http://mm-manager.cavokdev.com/import/parsetest/did/17/rid/18

    $this->_helper->layout()->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);

    $document_id = $this->_request->getParam('did', null);
    $row_id = $this->_request->getParam('rid', null);
    // $this->parseSavedData($document_id);
    $this->parseSavedRow($document_id, $row_id);
    echo '+++ DONE ++++++';
}

public function transactionAction()
{
    $this->_helper->layout()->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
        $importTransactionTable = new Application_Model_DbTable_Importtransaction();
        $importTransaction = $importTransactionTable->get(1);
        // var_dump($importTransaction);
    echo '+++ DONE ++++++';
}



    private function parseSavedData($document_id)
    {
        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $parser = $this->view->getHelper('DocumentHelper')->getParser($document_id);
        $parser->init($storageTable, $document_id);        //
        return true;
    }

    private function parseSavedRow($document_id, $row_id)
    {
        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $parser = $this->view->getHelper('DocumentHelper')->getParser($document_id);
        $parser->parseRow($storageTable, $document_id, $row_id);        //
        return true;
    }

    private function saveTransactionHistory($id, $history = null, $sheetname = null)
    {
        $sourceDocumentTable = new Application_Model_DbTable_Sourcedocuments();
        $row = $sourceDocumentTable->fetchRow($sourceDocumentTable->select()->where('id = ?', $id));
        $row->status  = 'Done';
        $row->history = json_encode($history);
        $row->sheet_nm = $sheetname;
        return $row->save();
    }

    /**
     * AJAX call that maps the source and destination columns
     * @return string HTML for the mapping UI
     */
    public function mapcolumnsAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // get the db table column names
        $destinationTable = new Application_Model_DbTable_Mpd();
        $destinationColumns = $destinationTable->info(Zend_Db_Table_Abstract::COLS);
        $destinationColumns = $this->removeHiddenColumns($destinationColumns);
        // get the worksheet column names
        try {
            $worksheetIndex = $this->_request->getParam('id', null);
            $source_document_id = $this->_request->getParam('sid', null);
            $columnNameRow = $this->_request->getParam('column_name_row', 1);
            $sourceDocumentTable = new Application_Model_DbTable_Sourcedocuments();
            $sourceDocument = $sourceDocumentTable->getFileById($source_document_id);
            $inputFileName = '../files/'. $sourceDocument['file_storage_nm'][0] . '/' . $sourceDocument['file_storage_nm'];

            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // **  Read the list of worksheet names and select the one that we want to load  **/
            $worksheetList = $objReader->listWorksheetNames($inputFileName);
            $sheetname = $worksheetList[$worksheetIndex];
            /**  Advise the Reader of which WorkSheets we want to load  **/
            $objReader->setLoadSheetsOnly($sheetname);
            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load($inputFileName);
            $sourceColumns = $objPHPExcel->getActiveSheet()->rangeToArray('A' . $columnNameRow . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $columnNameRow);
            $columnMapArray = $this->buildColumnMapArray($sourceColumns, $destinationColumns, $destinationTable);
            echo $this->buildMappingSelector($columnMapArray,$sourceColumns, $destinationColumns);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
    }

    /**
     * Builds the array that contains the column mapping information
     * @param  array $sourceColumns      worksheet column names
     * @param  array $destinationColumns DB table column names
     * @param  DB Table $destinationTable   DB table where data will be saved
     * @return array                     The mapped columns
     */
    public function buildColumnMapArray($sourceColumns, $destinationColumns, $destinationTable)
    {
        $tableInfo = $destinationTable->info('metadata');
        /**  Map the source to the destination table columns  **/
        $dest_options = array();
        $dest_options['ignore'] = "(ignore)";
        foreach($tableInfo as $column_name=>$schema) {
            $dest_options[$column_name] = $column_name." (".$schema['DATA_TYPE']." ".$schema['LENGTH'].")";
        }
        // hide certain DB table columns such as the ID
        $dest_options = $this->removeHiddenColumns($dest_options);
        // var_dump($dest_options);
        $mapping = array();
        if (isset($this->options['mapping_strategy'])) {
            if ($this->options['mapping_strategy']=='First Fit') {
                $t = range('A','Z');
                $i=0;
                foreach($sourceColumns as $key=>$value) {
                    $mapping[$key] = $t[$i++];
                }
            }
        }
        else if (isset($this->options['mapping'])) {
            foreach($sourceColumns as $key=>$value) {
                if(array_key_exists ($value,$this->options['mapping'])){
                    //$this->log->debug("found");
                    $mapping[$key] = $this->options['mapping'][$value];
                }
                else {
                    $mapping[$key] = "(ignore)";
                }
            }
        }
        else {
            foreach($sourceColumns[0] as $key=>$value) {
                $mapping[$key] = $this->findClosestMatchingString($value, $dest_options);
            }
        }
        return $mapping;
    }

    /**
     * Build the html for mapping the columns
     * @param  array $columnMapArray     The mapped columns
     * @param  array $sourceColumns      The worksheet columns
     * @param  array $destinationColumns The DB table columns
     * @return view                     import/mapcolumns.phtml
     */
    public function buildMappingSelector($columnMapArray,$sourceColumns, $destinationColumns)
    {
        $this->view->columnMapArray = $columnMapArray;
        $this->view->sourceColumns = $sourceColumns[0];
        $this->view->destinationColumns = $destinationColumns;
        $this->render('mapcolumns');
    }

    // define the mapping function
    function findClosestMatchingString($string, $dest_options)
    {
        $stringEditDistanceThreshold = 4;
        $closestDistanceThusFar = $stringEditDistanceThreshold + 1;
        $closestMatchValue      = null;
// var_dump($string,$dest_options);
        foreach ($dest_options as $key => $value) {
            // var_dump($key, $value, $s);
            $editDistance = levenshtein(strtolower($key),  strtolower($string));
            // var_dump($key." ".$string." ".$editDistance);

            // exact match
            if ($editDistance == 0) {
                return $key;

                // best match thus far, update values to compare against/return
            } elseif ($editDistance < $closestDistanceThusFar) {
                $closestDistanceThusFar = $editDistance;
                $closestMatchValue      = $key;
            }
        }

        //$this->log->debug($s." ".$closestMatchValue." ");
        return $closestMatchValue; // possible to return null if threshold hasn't been met
    }

    /**
     * Remove certain DB table required columns from the list the user sees
     * @param  array $array  The array of columns in the table
     * @param  array $hidden The columns to remove
     * @return array         The array with hidden columns removed
     */
    function removeHiddenColumns($array, $hidden = null)
    {
        if(is_null($hidden)){
            $hidden = array('mpd_id','document_id','crea_usr_id','crea_dtm','updt_usr_id','updt_dtm','deleted');
        }
        foreach($array as $key=>$value) {
            if (in_array($key,$hidden))
                unset($array[$key]);
            else
            if (in_array($value,$hidden))
                unset($array[$key]);
        }
        return $array;
    }

    // the number of user mapped columns must equal the number of columns in the source worksheet
    private function checkColumnCountIsEqual($mappedColumns, $sourceColumns)
    {
        if( count($mappedColumns) != count($sourceColumns) ){
            $this->view->error = array('state'=>'danger','message'=>'The number of columns you mapped doesn\'t equal the columns in the worksheet you uploaded.');
            $this->_redirect('/import/getimport');
        }else{
            return true;
        }
    }
}