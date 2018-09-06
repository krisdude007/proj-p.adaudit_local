<?php
class ExcelmacrosController extends Zend_Controller_Action
{

    private $User = null;

    private $customer_id = null;
    private $current_customer = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->config              = Zend_Registry::get('config');
        $this->acl                 = Zend_Registry::get('acl');
        $this->user                = Zend_Registry::get('user');
        //$this->applications        = Zend_Registry::get('applications');
        // $this->navigation          = Zend_Registry::get('navigation');

        $this->app                 = Zend_Registry::get('app');

        $this->customers           = Zend_Registry::get('customers');
        $this->current_customer    = Zend_Registry::get('current_customer');
        $this->user_role           = Zend_Registry::get('user_role');
         
    }

    public function indexAction()
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        
        // Get data from models
        $TestMsg = array();
        $TestMsg[0] = "Hello All";
        $TestMsg[1] = "Hello world 2";
        
        echo json_encode($TestMsg);
    }
    
    public function mrblookupAction()
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
    
        $mrb = $_GET['mrb'];
    
        // Get data from models
        // $TestMsg = array();
        // $TestMsg[0] = "Hello All ".$mrb;
        // $TestMsg[1] = "Hello world 2";
    
         $mrbTable = new Application_Model_DbTable_Mrb();
        $mrbList = $mrbTable->getMRBbyMRBnumber($mrb);
        echo json_encode($mrbList);
    }
    
}