<?php
class ApiController extends Zend_Controller_Action{

    public function init(){

        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        //*********** EXPERIMENT -- See doAjax.js file ***********/
        // $header = apache_request_headers();
        // $tokenKey = '1528cdb1c39205850bdb6713f0b1ab1509bd52de';
        // if($tokenKey != $header{'X-API-TOKEN'}){
        //     throw new Exception('You are not authorized to use this service.', 401);
        //     $this->getResponse()->setHttpResponseCode(401);
        //     exit;
        // }

        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->project_id = $this->_request->getParam('project_id',0);
        $this->view->project_id = $this->project_id;
    }

    public function mpdAction()
    {
        $document_id = $this->_request->getParam('id', null);
        $document_version_id = $this->_request->getParam('vid', null);

        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $records = $storageTable->getByDocIdNoIntervals($document_id, $document_version_id);
        $versionTable = new Application_Model_DbTable_Documentversions();
        $version = $versionTable->get($document_version_id);
        echo json_encode( array('success' => 'ok', 'data' => $records, 'version' => $version));
    }

    public function getrowAction()
    {
        $document_id = $this->_request->getParam('id', null);
        $document_version_id = $this->_request->getParam('vid', null);
        $row_id = $this->_request->getParam('rid', null);

        $documentTable = new Application_Model_DbTable_Documents();
        $fields = $documentTable->getFields($document_id);
        $storageTable = $this->view->getHelper('DocumentHelper')->getStorageTable($document_id);
        $row['data'] = $storageTable->getRowWithIntervals($document_id, $document_version_id, $row_id);
        // var_dump($fields);
        $row['fields'] = $fields;
        $row['message'] = 'ok';
        echo json_encode($row);
    }

    public function getmanufacturersAction()
    {
        $type = $this->_request->getParam('type','all');
        $manufacturerTable = new Application_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturerTable->getManufacturers($type);
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $manufacturers;
            echo json_encode($row);
        }else{
            return $manufacturers;
        }
    }

    // public function getmanufacturerselectAction(){

    //     $manufacturers = $this->getmanufacturersAction();
    //     $select = '<select class="form-control" name="manufacturer">';
    //     $select .= '<option disabled selected>Select Manufacturer...</option>';
    //     foreach($manufacturers as $manufacturer){
    //         $select .= '<option value="'.$manufacturer['manufacturer_id'].'">'.$manufacturer['manufacturer_name'].'</option>';
    //     }
    //     $select .= '</select>';
    //     echo $select;
    // }

    public function getmodelsAction($id=null)
    {
        $aircraftTable = new Application_Model_DbTable_Aircraft();
        return $aircraftTable->getAircraft($id);
    }

    public function getmodelselectAction()
    {
        $id = $this->_request->getParam('id',0);
        $aircraft = $this->getmodelsAction($id);
        $select = '<select class="form-control" name="aircraft">';
        $select .= '<option disabled selected>Select Aircraft...</option>';
        foreach($aircraft as $item){
            $select .= '<option value="'.$item['aircraft_id'].'">'.$item['aircraft_model'].'</option>';
        }
        $select .= '</select>';
        echo $select;
    }

    public function gettcdsbymanufacturerAction()
    {
        $id = $this->_request->getParam('id',0);
        if($id){
            $tcdsTable = new Application_Model_DbTable_Tcds();
            $tcds = $tcdsTable->gettcdsByManufacturer($id);
            $row['message'] = 'ok';
            $row['data'] = $tcds;
        }else{
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

    public function getaircraftAction()
    {
        $id = $this->_request->getParam('id',0);
        $aircraftTable = new Application_Model_DbTable_Aircraft();
        $aircraft = $aircraftTable->getAircraftByID($id);
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $aircraft;
            echo json_encode($row);
        }else{
            return $aircraft;
        }
    }

    public function getaircraftlistAction()
    {
        $aircraftTable = new Application_Model_DbTable_Aircraft();
        $aircraft = $aircraftTable->getAircraft();
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $aircraft;
            echo json_encode($row);
        }else{
            return $aircraft;
        }
    }
    public function gettcdslistAction()
    {
        $tcdsTable = new Application_Model_DbTable_Tcds();
        $type = $this->_request->getParam('type','aircraft');
        $tcds = $tcdsTable->gettcds($type);
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $tcds;
            echo json_encode($row);
        }else{
            return $tcds;
        }
    }

    /**
     * returns all engine data
     * @return json all engine data
     */
    public function getenginelistAction()
    {
        $engineTable = new Application_Model_DbTable_Engines();
        $engines = $engineTable->getAllEngineData();
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $engines;
            echo json_encode($row);
        }else{
            return $engines;
        }
    }
    /**
     * returns all propellors data
     * @return json all propellors data
     */
    public function getpropellorlistAction()
    {
        $propellorsTable = new Application_Model_DbTable_Propellors();
        $propellorss = $propellorsTable->getAllPropellorData();
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $propellorss;
            echo json_encode($row);
        }else{
            return $propellorss;
        }
    }

    /**
     * return a list of engines from a %term% SQL call
     * This is used for type-ahead look ups.
     * @return json list of engines that match %term%
     */
    public function getenginemodelsAction()
    {
        $term = $this->_request->getParam('term',0);
        $engineTable = new Application_Model_DbTable_Engines();
        $engines = $engineTable->getEngineModels($term);
        $flat = $this->flatten_array($engines);
        echo json_encode( $flat );
    }

    /**
     * return engine data for an engine_id
     * @return json engine data
     */
    public function getengineAction(){
        $engine_id = $this->_request->getParam('engine_id',0);

    }

    // :FIXME - MAYBE DELETE ME
    // /**
    //  * return all manufacturere info
    //  * @return json all manufacturer data
    //  */
    // public function getmanufacturerlistAction()
    // {
    //     $manufacturerTable = new Application_Model_DbTable_Manufacturer();
    //     echo json_encode($manufacturerTable->getManufacturers('all'));
    // }

    /**
     * return all data for a single manufacturer
     * @return json manufacturer info
     */
    public function getmanufacturerAction()
    {
        $id = $this->_request->getParam('id',0);
        
        $manufacturerTable = new Application_Model_DbTable_Manufacturer();
        echo json_encode($manufacturerTable->get($id));
    }
    
    public function getadsbyfleetidsAction()
    {   
        if (isset($_SESSION['fleets']) && !is_null($_SESSION['fleets'])) {
            $fleets = $_SESSION['fleets'];
            $adTable = new Application_Model_DbTable_Ad();
            echo json_encode($adTable->getAdsByFleetId($fleets));
        } else {
            $this->_redirect('/');
        }
    }
    
    public function getadsforfinalreportAction()
    {   
        $adId = isset($_SESSION['adId']) ? $_SESSION['adId'] : 0;
        $fleets = isset($_SESSION['fleets']) ? $_SESSION['fleets'] : 0;
        $reportTable = new Application_Model_Report();
        
        if ($adId > 0) {
            $reportResult = $reportTable->getFinalReport($fleets);
            echo json_encode($reportResult);
        } else {
            $this->_redirect('/ad/index');
        }
    }
    
    public function getparaghsbyadAction()
    {   
        $adId = isset($_SESSION['adId']) ? $_SESSION['adId'] : 0;
        $adParagraphTable = new Application_Model_DbTable_AdParagraph();
        
        if ($adId > 0) {
            $adParagraphs = $adParagraphTable->getParagraphs($adId);
            echo json_encode($adParagraphs);
        } else {
            $this->_redirect('/ad/index');
        }
    }

    /**
     * return a list of ads from a %term% SQL call
     * This is used for type-ahead look ups.
     * @return json list of ads that match %term%
     */
    public function getadnumbersAction()
    {
        $term = $this->_request->getParam('term',0);
        $adTable = new Application_Model_DbTable_Ad();
        $ads = $adTable->getAdNumbers($term);
        echo json_encode($ads);
    }

    // public function getadoptionsAction()
    // {
    //     //$type = $this->_request->getParam('type','aircraft');
    //     $adTable = new Application_Model_DbTable_Ad();
    //     $ads = $adTable->getAdDataOptions('null');
    //     if($this->_request->isXmlHttpRequest()){
    //         $row['message'] = 'fail';
    //         $row['data'] = $ads;
    //         echo json_encode($row);
    //     }else{
    //         return $ads;
    //     }
    // }

     // $options (model&func required)
    public function getselectoptionsAction()
    {
        $options = $this->_request->getParam('options',null);
        if(!isset($options['params'])) $options['params'] = '';

        $table = new $options['dbTable'];
        $response = $table->$options['method']($options['params']);//var_dump($response);exit;
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $response;
            echo json_encode($row);
        }else{
            return $response;
        }
    }
    
    public function getstatusoptionsAction()
    {
        $options = $this->_request->getParam('options',null);
        if(!isset($options['params'])) $options['params'] = '';

        $table = new $options['dbTable'];
        $response = $table->$options['method']($options['params']);
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $response;
            echo json_encode($row);
        }else{
            return $response;
        }
    }

    public function getdocumentAction()
    {
        $did = $this->_request->getParam('did',null);
        $table = new Application_Model_DbTable_Documents();
        $response = $table->get($did);     
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $response;
            echo json_encode($row);
        }else{
            return $response;
        }
    }
    public function getdocumentversionAction()
    {
        $vid = $this->_request->getParam('vid',null);
        $table = new Application_Model_DbTable_Documentversions();
        $response = $table->get($vid);     
        if($this->_request->isXmlHttpRequest()){
            $row['message'] = 'ok';
            $row['data'] = $response;
            echo json_encode($row);
        }else{
            return $response;
        }
    }

    public function flatten_array($array)
    {
        $flattened_array = array();
            array_walk_recursive($array, function($a) use (&$flattened_array) { $flattened_array[] = $a; });
        return array_values($flattened_array);
    }
}