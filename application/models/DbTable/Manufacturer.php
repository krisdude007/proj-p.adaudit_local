<?php

class Application_Model_DbTable_Manufacturer extends Zend_Db_Table_Abstract
{

    protected $_name = 'manufacturer';
    protected $_primary = 'id';

    function get($id)
    {
        $sql = "SELECT *
                FROM manufacturer
                WHERE id = $id
                ";
       $query = $this->getAdapter()->quoteInto($sql,'');
       $result = $this->getAdapter()->fetchRow($query);
       return $result;
    }
    function getName($id)
    {
        $sql = "SELECT manufacturer_name
                FROM manufacturer
                WHERE id = $id
                ";
       $query = $this->getAdapter()->quoteInto($sql,'');
       $result = $this->getAdapter()->fetchRow($query);
       return $result['manufacturer_name'];
    }

    function getManufacturers($type='all')
    {
        if($type == 'all'){
        $sql = "SELECT *
                    FROM manufacturer
                    WHERE deleted = 0
                    ORDER BY manufacturer_name
                    ";
        }else{
            $sql = "SELECT *
                    FROM manufacturer
                    WHERE manufacturer_type = '$type'
                    AND deleted = 0
                    ORDER BY manufacturer_name
                    ";
        }
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
                
    function getManufacturerOptions($type='aircraft')
    {
     
        $sql = "SELECT  id as value,
                        manufacturer_name as valueText
                FROM manufacturer
                WHERE
                    manufacturer_type = '$type' 
                    AND deleted = 0
                ORDER BY manufacturer_name
        ";
        $query = $this->getAdapter()->quoteInto($sql,'');
        return $this->getAdapter()->fetchAll($query);
    }
}