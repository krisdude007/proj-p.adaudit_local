<?php

class IndexController extends Zend_Controller_Action {

    public $adsTable;

    public function init() {

        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        //date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->project_id = $this->_request->getParam('project_id', 0);
        $this->view->project_id = $this->project_id;

        @session_start();

        $this->adsTable = new Application_Model_DbTable_Ad();
    }

    public function indexAction() {
        $this->view->curFleets = array();
        $fleetTable = new Application_Model_DbTable_Fleet();
        $this->view->fleets = $fleetTable->getFleets();
        if (isset($_SESSION['fleets']) && isset($_SESSION['fleets']) != '') {
            $this->view->curFleets = explode(',', $_SESSION['fleets']);
        }
    }

    public function createAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_request->getParam('data', null);
        //var_dump($data);exit;
        $formData = $this->view->getHelper('DocumentHelper')->formatAjaxFormMultiselect($this->_request->getParam('data', null, array('applicabilty,')));
        $formData['user_id'] = $this->user['user_id'];
        try {
            $this->adTable->newAd($formData);
            $row['message'] = 'ok';
        } catch (Exception $e) {
            var_dump($e);
            exit;
            $row['message'] = 'fail';
        }
    }

    public function editAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $row['message'] = 'ok';
        echo json_encode($row);
    }

    public function itemAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', null); //var_dump($id);exit;
        $item = $this->adsTable->getAdItem($id);

        $row['message'] = 'ok';
        $row['data'] = $item;
        echo json_encode($row);
    }

    public function updateAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_request->getParam('data', null);
        $selectname = $this->_request->getParam('selectname', null);

        $accessArray = array();
        $tempArray = array();

        // this foreach extracts data from the AJAX form post and puts it into arrays
        foreach ($data as $key => $item) {
            if ($item['name'] == 'id') {
                $id = $item['value']; // extract the id of the ad item that is being updated
            }
            if ($item['name'] == $selectname) {
                // extract the selected user access rights for the ad
                $accessArray[] = $item['value'];
            } else {
                // this is one of the other form elements
                $tempArray[$item['name']] = $item['value'];
            }
        }

        //$access = implode(',', $accessArray); // turn the access rights into a comma delimited string

        $where = $this->adTable->getAdapter()->quoteInto('id = ?', $id);
        try {
            $this->adTable->update(array(
                'number' => $tempArray['number'],
                'manufacturer_id' => $tempArray['manufacturer_name'],
                'effective_date' => $tempArray['effective_date'],
                'affected_ad' => $tempArray['affected_ad_label'],
                'ata' => $tempArray['ata'],
                'reason' => $tempArray['reason'],
                'updt_usr_id' => $this->user['user_id'],
                'updt_dtm' => date('Y/m/d H:i:s', time()),
                    )
                    , $where);
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

        $this->_redirect('/ad/index');
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', 0);

        $where = $this->adTable->getAdapter()->quoteInto('id = ?', $id);
        try {
            $this->adTable->update(array('deleted' => 1), $where);
            $row['message'] = 'ok';
        } catch (Exception $e) {
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

}
