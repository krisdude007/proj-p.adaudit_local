<?php

class PHPSlickGrid_JSON {
    
    private $server = null;
    
    private $Config = null;
    private $Table  = null;
    
    function __construct($Table=null,$Config=null) {
        
        if (is_object($Table)) {
            if (get_class($Table)=='Zend_Db_Table_Abstract') {
                $this->Table=get_class($Table);
            }
            else {
                $this->Table = $Table;
            }
        }
        
        $this->Config=$Config;
                
        // Instantiate JSON RPC srver
        $this->server = new Zend_Json_Server();
        //$this->server->setClass('PHPSlickGrid_JSON_DbTable_DataAccess');  
        
        /*******************************************************************
         * For now I am just seperating these into classes to make maintance
         * easer.  For the next iteration these should be in a plugin
         * architecture. 
         *******************************************************************/
        $this->server->setClass('PHPSlickGrid_JSON_ListFilter2');
        $this->server->setClass('PHPSlickGrid_JSON_Meta');
        $this->server->setClass('PHPSlickGrid_JSON_Options');
        $this->server->setClass('PHPSlickGrid_JSON_DataCache');  //PHPSlickGrid_JSON_DbTable2_DataCache

    }
    
    
    function handle() {
        
        // Save the parameters for later use by the DataAccess class
        $parameters = array();
        $parameters['table']=$this->Table;
        $parameters['config']=$this->Config;
        
       // Zend_Registry::set('PHPSlickGrid_JSON_DbTable_DataAccess', $parameters);
        Zend_Registry::set('PHPSlickGrid_JSON', $parameters);
        
        // Process JSON
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // Indicate the URL endpoint, and the JSON-RPC version used:
            $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $this->server->setTarget($uri)
            ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
        
            // Return the SMD to the client
            header('Content-Type: application/json');
            echo $this->server->getServiceMap();
            return;
        }
      
        $this->server->handle();
    }
    
}