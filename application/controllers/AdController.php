<?php

class AdController extends Zend_Controller_Action {

    public $adTable;
    public $customerId;
    public $urlPath;

    public function init() {
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->adTable = new Application_Model_DbTable_Ad();
        $this->urlPath = isset($_SERVER['HTTPS']) ? "https://" : "http://" . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        @session_start();

        $this->customerId = 1;
    }

    public function indexAction() {
        
        if (empty($_POST)) {
            //unset($_SESSION['fleets']);
            //$this->_redirect('/');
        }
            if (isset($_POST) && isset($_POST['fleets']) != '') {
                $data = $this->_request->getParam('fleets', null);
                $fleets = implode(',', $data);
                $_SESSION['fleets'] = $fleets;
            } else {
                //at any point, need to check if it comes here
            }
    }

    public function createAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_request->getParam('data', null);

        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxFormMultiselect($this->_request->getParam('data', null, array('applicabilty,')));
        $formData['user_id'] = $this->user['user_id'];
        try {
            $this->adTable->newAd($formData);
            $row['message'] = 'ok';
        } catch (Exception $e) {
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

    public function editAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', null);
        $item = $this->adTable->getAdItem($id);

        $row['message'] = 'ok';
        $row['data'] = $item;
        echo json_encode($row);
    }
    
    public function viewAction() {

        $id = $this->_request->getParam('id', null);
        
        $fleetTable = new Application_Model_DbTable_Fleet();
        
        $item = $this->adTable->getAdById($id);
        $fleets = $fleetTable->getFleetListByAdId($id);
        
        $this->view->adItem = $item;
        $this->view->fleetList = $fleets[0]['fleet_txt'];
    }

    public function itemAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', null);
        $item = $this->adTable->getAdById($id);

        $row['message'] = 'ok';
        $row['data'] = $item;
        echo json_encode($row);
    }

    public function updateAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $adFleetTable = new Application_Model_DbTable_AdFleet();
        $adAffectedTable = new Application_Model_DbTable_AdAffected();

        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxFormMultiselect($this->_request->getParam('data', null, array('applicabilty,')));
        $formData['user_id'] = $this->user['user_id'];//var_dump($formData);exit;

        $adId = $formData['ad_id'][0];
        $adTxt = $formData['ad_txt'][0];
        $adDesc = $formData['ad_desc'][0];
        $adStatusId = $formData['ad_status_id'][0];
        $fleet = $formData['applicability'];
        $affectedAds = isset($formData['affectedAds']) ? $formData['affectedAds'] : null;
        $effectiveDate = $formData['effective_date'][0];
        $referenceTxt = $formData['reference_txt'][0];
        $ataId = empty($formData['ata_id']) ? $formData['ata_id'] : null;

        // $adRec = $this->adTable->find($adId)->toArray();
        // $curAdTxt = $adRec[0]['ad_txt'];
        // $curAdDesc = $adRec[0]['ad_desc'];
        // $curAdStatusId = $adRec[0]['ad_status_id'];
        // $curEffectiveDate = $adRec[0]['effective_date'];
        // $curReferenceTxt = $adRec[0]['reference_txt'];
        // $curAtaId = $adRec[0]['ata_id'];

        $where = $this->adTable->getAdapter()->quoteInto('ad_id = ?', $adId);
        try {

            $data = array(
                'ad_txt' => $adTxt,
                'ad_desc' => $adDesc,
                'effective_date' => $effectiveDate,
                'reference_txt' => $referenceTxt,
                'ata_id' => $ataId,
                'updt_usr_id' => $this->user['user_id'],
                'ad_status_id' => $adStatusId,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'url_path' => $this->urlPath);
            $this->adTable->update($data, $where);
            
        } catch (Exception $e) {//var_dump($e);
            $row['message'] = $e->getMessage();
        }
        
        //delete affectedAD rec to avoid duplication
        try {
            $adAffectedTable->delete($adId);
            //upadate adFleet rec
            if (!is_null($affectedAds)) {
            $adAffectedTable->addAffectedAds($adId, $affectedAds, $this->user['user_id']);
            }
            $row['message'] = 'ok';
        } catch (Exception $e) {
            $row['message'] = $e->getMessage();
        }
        
        //delete adfleet rec to avoid duplication
        try {
            $adFleetTable->delete($adId);
            //upadate adFleet rec
            $adFleetTable->add($adId, $fleet, $this->user['user_id']);
            $row['message'] = 'ok';
        } catch (Exception $e) {
            $row['message'] = $e->getMessage();
        }

        echo json_encode($row);
    }

    public function showAction() {
        $id = $this->_request->getParam('id', 0);
        $ad = $this->adTable->get($id);

        $this->view->ad = $ad;
    }

    public function storeAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // $engine_model           = $this->_request->getParam('engine_model',0);
        // $engine_manufacturer_id = $this->_request->getParam('engine_manufacturer_id',0);
        // $tcds_id                = $this->_request->getParam('tcds_id',0);
        // $new_tcds_row                     = $this->engineTable->createRow();
        //     $new_tcds_row->engine_manufacturer_id = $this->_request->getParam('engine_manufacturer_id',0);
        //     $new_tcds_row->engine_tcds_id         = $this->_request->getParam('tcds_id',0);
        //     $new_tcds_row->engine_model           = strtoupper($this->_request->getParam('engine_model',0));
        //     $new_tcds_row->crea_usr_id            = $this->user['user_id'];
        //     $new_tcds_row->crea_dtm               = date('Y/m/d H:i:s',time());
        // $document_id = $new_tcds_row->save();
        $this->_redirect('/ad/index');
    }

    public function unlinkAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $adCustomerTable = new Application_Model_DbTable_AdCustomer();
        $id = $this->_request->getParam('id', 0);


        try {
            $where = array();
            $where[] = $adCustomerTable->getAdapter()->quoteInto('ad_id = ? ', $id);
            $where[] = $adCustomerTable->getAdapter()->quoteInto('customer_id = ? ', $this->customerId);

            if ($adCustomerTable->update(array('deleted' => 1), $where)) {

                $getUpdatedRecList = $adCustomerTable->getAdByAdIdAndCustomerId($id, $this->customerId);

                foreach ($getUpdatedRecList as $g) {
                    // $data = array();
                    // $data['table_nm'] = $adCustomerTable->getName();
                    // $data['key_column_nm'] = 'ad_customer_id';
                    // $data['key_column_value'] = $g['ad_customer_id'];
                    // $data['modified_column_nm'] = 'deleted';
                    // $data['old_value'] = 0;
                    // $data['new_value'] = 1;
                    // $data['crea_usr_id'] = $this->user['user_id'];

                    // $auditLog = $this->auditLogTable->add($data);
                }
            } else {
                //TO-DO: contingency plan
            }

            $row['message'] = 'ok';
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', 0);


        try {
            $where = array();
            $where[] = $this->adTable->getAdapter()->quoteInto('ad_id = ? ', $id);

            $result = $this->adTable->update(array('deleted' => 1,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'url_path' => $this->urlPath), $where);

            $row['message'] = 'ok';
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

    public function inactivateAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $formData = $this->_request->getParam('data', ''); //var_dump($formData[0]['value']);exit;
        $id = $formData[0]['value'];

        try {
            $where = array();
            $where[] = $this->adTable->getAdapter()->quoteInto('ad_id = ? ', $id);

            $result = $this->adTable->update(array('is_active' => 0, 'is_active_comment' => $formData[1]['value'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'url_path' => $this->urlPath), $where);

            $row['message'] = 'ok';
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

    public function supercedeAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxFormMultiselect($this->_request->getParam('data', null, array('supercededAds,')));
        $adId = $formData['ad_id'][0];

        if (empty($formData['supercededAds'])) {
            $setSupercededAd = $this->adTable->setSupercededAd($adId, $this->user['user_id'], true);
            $setIsSuperceding = $this->adTable->setIsSuperceding($adId, 0, $this->user['user_id']);
            $row['message'] = 'ok';
        } else {
            $supercededAds = $formData['supercededAds'];
            $curSupercededAdArr = array();

            foreach ($supercededAds as $su) {
                $supercededRec = $this->adTable->find($su)->toArray();
                $curSupercededAdArr[$su] = $supercededRec[0]['superceded_ad_id'];
            }

            try {
                $setSupercededAd = $this->adTable->setSupercededAd($adId, $this->user['user_id'], true);
                $setIsSuperceding = $this->adTable->setIsSuperceding($adId, 1, $this->user['user_id']);

                if ($setIsSuperceding == true) {
                    foreach ($supercededAds as $su) {
                        $res = $this->adTable->updateSupercededAd($adId, $su, $this->user['user_id']);
                    }
                } else {
                    //TO-DO: contingency plan
                }

                $row['message'] = 'ok';
            } catch (Exception $e) {//var_dump($e);exit;
                $row['message'] = 'fail';
            }
        }
        echo json_encode($row);
    }

    public function reactivateAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', 0);

        try {
            $where = array();
            $where[] = $this->adTable->getAdapter()->quoteInto('ad_id = ? ', $id);

            $result = $this->adTable->update(array('is_active' => 1,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'url_path' => $this->urlPath), $where);

            $row['message'] = 'ok';
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

}
