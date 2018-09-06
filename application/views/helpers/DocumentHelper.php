<?php

class Zend_View_Helper_DocumentHelper extends Zend_View_Helper_Abstract 
{
    /**
     * Return the document records storage table
     * @param  int $document_id 
     * @return model the model for the table that contains the documents reccords
     */
    public function getStorageTable($document_id)
    {
        $documentTable = new Application_Model_DbTable_Documents();
        $document = $documentTable->get($document_id);
        $documentTypeTable = new Application_Model_DbTable_Documenttype();
        $document_type = $documentTypeTable->get($document['document_type_id']);
        $type = ucfirst( strtolower($document_type['type']) );
        $documentTable = "Application_Model_DbTable_$type";
        return new $documentTable();
    }

    /**
     * return the applicableparser the document
     * @param  int $document_id 
     * @return object the document parser
     */
    public function getParser($document_id)
    {
        $documentTable = new Application_Model_DbTable_Documents();
        $document = $documentTable->get($document_id);
        $manufacturerTable = new Application_Model_DbTable_Manufacturer();
        $manufacturer = $manufacturerTable->getName( $document['manufacturer_id'] );
        $parserName = "ICAparsers_$manufacturer";
        return new $parserName();
    }

    public function getMessages()
    {
        $messages['success'] = $this->_helper->getHelper('FlashMessenger')->getMessages('Success');
        $messages['error'] = $this->_helper->getHelper('FlashMessenger')->getMessages('Error');
        return $messages;
    }

    public function formatAjaxForm($data)
    {
        $temp = array();
        foreach ($data as $key => $value) {
            $temp[$value['name']]=$value['value'];
        }
        return $temp;
    }

    public function formatAjaxFormMultiselect($data)
    {
        $temp = array();
        foreach ($data as $key => $value) {
            $temp[$value['name']][] = $value['value'];
        }
        return $temp;
    }
}