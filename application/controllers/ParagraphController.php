<?php

class ParagraphController extends Zend_Controller_Action {

    public $adParagraphTable;
    public $paragraphOptionTable;
    public $customerId;
    public $urlPath;

    public function init() {
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->adParagraphTable = new Application_Model_DbTable_AdParagraph();
        $this->adTable = new Application_Model_DbTable_Ad();
        $this->paragraphOptionTable = new Application_Model_DbTable_ParagraphOption();
        $this->urlPath = isset($_SERVER['HTTPS']) ? "https://" : "http://" . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        @session_start();

        $this->customerId = 1;
    }

    public function indexAction() {
        
        $id = $this->_request->getParam('id', null);
        $_SESSION['adId'] = $id;
        $appId = Zend_Registry::get('config')->application_id;
        
        $adRec = $this->adTable->find($id)->toArray();
        $this->view->users = $this->adTable->getUsers($appId);
        $this->view->auditedBy = $adRec[0]['audited_by'];
        $this->view->adRec = $adRec;
    }

    public function editAction() {

        $id = $this->_request->getParam('id', null);

        $adParagraph = $this->adParagraphTable->find($id)->toArray();
        $adParagraphId = $adParagraph[0]['ad_paragraph_id'];
        $paragraphOptions = $this->paragraphOptionTable->getParagraphOptions($adParagraphId);

        $this->view->adParagraph = $adParagraph;
        $this->view->paragraphOptions = $paragraphOptions;
    }

    public function createemptyAction() {

        $id = $this->_request->getParam('id', null);
        $newId = $this->paragraphOptionTable->add($id);

        $row['newId'] = $newId;
        echo json_encode($row);
    }

    public function createAction() {
        //$this->_helper->layout()->disableLayout(true);
        //$this->_helper->viewRenderer->setNoRender(true);

        $adParagraphTable = new Application_Model_DbTable_AdParagraph();
        $paragraphOptionTable = new Application_Model_DbTable_ParagraphOption();

        $adParagraph = $adParagraphTable->find(0)->toArray();
        $paragraphOptions = $paragraphOptionTable->getParagraphOptions(0);

        $htmlView = $this->view->render('/paragraph/edit.phtml');
        $this->view->paragraphOptions = $paragraphOptions;
        echo $htmlView;
    }

    public function updateAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $btnClicked = $this->_request->getParam('submit', 0);

        $id = $this->_request->getParam('ad_paragraph_id', null);
        $paragraphOptions = $this->paragraphOptionTable->getParagraphOptions($id);
        $paragraphTxt = $this->_request->getParam('ad_para_name', null);
        $paragraphIf = $this->_request->getParam('ad_para_if', null);
        $paragraphNotes = $this->_request->getParam('ad_para_notes', null);
        $paragraphDo = $this->_request->getParam('ad_para_do', null);

        //var_dump($_POST);exit;

        if (empty($id)) {
            $data = array(
                'ad_id' => $_SESSION['adId'],
                'paragraph_txt' => $paragraphTxt,
                'if_txt' => $paragraphIf,
                'notes_txt' => $paragraphNotes,
                'do_txt' => $paragraphDo,
                'user_id' => $this->user['user_id']);
            $newParagraphId = $this->adParagraphTable->add($data);
            if ($newParagraphId > 0) {
                $this->_redirect('/paragraph/edit/id/' . $newParagraphId);
            }
        }

        $where = $this->adParagraphTable->getAdapter()->quoteInto('ad_paragraph_id = ?', $id);
        try {
            $this->adParagraphTable->update(array(
                'paragraph_txt' => $paragraphTxt,
                'if_txt' => $paragraphIf,
                'notes_txt' => $paragraphNotes,
                'do_txt' => $paragraphDo,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'url_path' => $this->urlPath,
                'crea_usr_id' => $this->user['user_id'],
                'crea_dtm' => date('Y/m/d H:i:s', time()),
                'updt_usr_id' => $this->user['user_id']), $where);

            foreach ($paragraphOptions as $option) {
                $optionId = $option['paragraph_option_id'];
                $optionTxt = $this->_request->getParam('option_txt_' . $optionId, null);
                $limitTxt = $this->_request->getParam('limit_txt_' . $optionId, null);
                $fromTxt = $this->_request->getParam('from_txt_' . $optionId, null);
                $orTxt = $this->_request->getParam('or_txt_' . $optionId, null);
                $exceptionTxt = $this->_request->getParam('exception_txt_' . $optionId, null);
                $deleted = $this->_request->getParam('delete_' . $optionId, null);

                $curOptionTxt = $option['option_txt'];
                $curLimitTxt = $option['limit_txt'];
                $curFromTxt = $option['from_txt'];
                $curOrTxt = $option['or_txt'];
                $curExceptionTxt = $option['exception_txt'];

                $where = $this->paragraphOptionTable->getAdapter()->quoteInto('paragraph_option_id = ?', $optionId);
                if ($optionTxt != $curOptionTxt || $limitTxt != $curLimitTxt || $fromTxt != $curFromTxt || $orTxt != $curOrTxt || $exceptionTxt != $curExceptionTxt) {
                    //var_dump($optionTxt, $curOptionTxt);exit;
                    $this->paragraphOptionTable->update(array(
                        'option_txt' => $optionTxt,
                        'from_txt' => $fromTxt,
                        'limit_txt' => $limitTxt,
                        'or_txt' => $orTxt,
                        'exception_txt' => $exceptionTxt,
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'url_path' => $this->urlPath,
                        'crea_usr_id' => $this->user['user_id'],
                        'crea_dtm' => date('Y/m/d H:i:s', time()),
                        'updt_usr_id' => $this->user['user_id']), $where);
                }

                if (!is_null($deleted) && $deleted == 1) {
                    $this->paragraphOptionTable->update(array(
                        'deleted' => 1,
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'url_path' => $this->urlPath,
                        'crea_usr_id' => $this->user['user_id'],
                        'crea_dtm' => date('Y/m/d H:i:s', time()),
                        'updt_usr_id' => $this->user['user_id']), $where);
                }
            }

            if ($btnClicked == 'Save and Return') {
                $this->_redirect('/paragraph/index/id/' . $_SESSION['adId']);
            } else {
                $this->_redirect('/paragraph/edit/id/' . $id);
            }
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = $e->getMessage();
        }
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', 0);

        try {
            $where = array();
            $where[] = $this->adParagraphTable->getAdapter()->quoteInto('ad_paragraph_id = ? ', $id);

            $result = $this->adParagraphTable->update(array('deleted' => 1, 'url_path' => $this->urlPath, 'ip_address' => $_SERVER['REMOTE_ADDR'], 'updt_usr_id' => $this->user['user_id']), $where);

            $row['message'] = 'ok';
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }

    public function setactionableAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', 0);
        $actionable = $this->_request->getParam('actionable', 0);
        $actionable = ($actionable == 1) ? 0 : 1; 

        try {
            $where = array();
            $where[] = $this->adParagraphTable->getAdapter()->quoteInto('ad_paragraph_id = ? ', $id);
            $result = $this->adParagraphTable->update(array('is_actionable' => $actionable, 'url_path' => $this->urlPath, 'ip_address' => $_SERVER['REMOTE_ADDR'], 'updt_usr_id' => $this->user['user_id']), $where);

            $row['message'] = 'ok';
        } catch (Exception $e) {//var_dump($e);exit;
            $row['message'] = 'fail';
        }
        echo json_encode($row);
    }
    
    public function viewAction() {

        $id = $this->_request->getParam('id', null);
        $adParagraph = $this->adParagraphTable->find($id)->toArray();
        
        $adRec = $this->adTable->find($adParagraph[0]['ad_id'])->toArray();
        $adTxt = $adRec[0]['ad_txt'];
        
        $adParagraphId = $adParagraph[0]['ad_paragraph_id'];
        $paragraphOptions = $this->paragraphOptionTable->getParagraphOptions($adParagraphId);
        
        $isActionable = $adParagraph[0]['is_actionable'];

        $this->view->adParagraph = $adParagraph;
        $this->view->adTxt = $adTxt;
        $this->view->isActionable = $isActionable;
        $this->view->paragraphOptions = $paragraphOptions;
    }
    
    public function updateauditAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('ad_id', 0);
        $auditedBy = $this->_request->getParam('audited_by', 0);
        $auditStartDt = $this->_request->getParam('audit_start_dt', 0);
        $auditEndDt = $this->_request->getParam('audit_end_dt', 0);
        
        try {
            $where = array();
            $where[] = $this->adTable->getAdapter()->quoteInto('ad_id = ? ', $id);
            $result = $this->adTable->update(array('audited_by' => $auditedBy, 'audit_start_dt' => $auditStartDt, 'audit_end_dt' => $auditEndDt,'url_path' => $this->urlPath, 'ip_address' => $_SERVER['REMOTE_ADDR'], 'updt_usr_id' => $this->user['user_id']), $where);

            $this->_redirect("/paragraph/index/id/$id");
        } catch (Exception $e) {var_dump($e);
            $this->_redirect("/paragraph/index/id/$id");
        }
        
    }

}
