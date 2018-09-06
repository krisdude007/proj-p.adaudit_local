<?php

class Application_Model_Report extends Zend_Db_Table_Abstract {

    public function getFinalReport($fleets, $filterBy) {
        $actionFilter = '';
        if ($filterBy != '') {
            $actionFilter = " and b.is_actionable = $filterBy ";
        }
        
        $sql = "SELECT
                    a.ad_id ,
                    a.ad_txt ,
                    COALESCE(f.ad_count , 1) as ad_count ,
                    b.ad_paragraph_id ,
                    b.paragraph_txt ,
                    b.if_txt ,
                    COALESCE(d.option_count , 1) AS option_count ,
                    c.option_txt ,
                    b.do_txt ,
                    c.limit_txt ,
                    c.from_txt ,
                    c.or_txt ,
                    c.exception_txt,
                    c.paragraph_option_id
            FROM
                    ad a
            LEFT JOIN ad_paragraph b ON a.ad_id = b.ad_id $actionFilter
            AND a.deleted = 0
            AND b.deleted = 0
            LEFT JOIN paragraph_option c ON b.ad_paragraph_id = c.ad_paragraph_id
            AND b.deleted = 0
            AND c.deleted = 0
            LEFT JOIN(
                    SELECT
                            c.ad_paragraph_id ,
                            count(*) AS option_count
                    FROM
                            ad a ,
                            ad_paragraph b ,
                            paragraph_option c
                    WHERE
                            a.ad_id = b.ad_id $actionFilter
                    AND b.ad_paragraph_id = c.ad_paragraph_id
                    AND a.deleted = 0
                    AND b.deleted = 0
                    AND c.deleted = 0
                    GROUP BY
                            c.ad_paragraph_id
            ) d ON c.ad_paragraph_id = d.ad_paragraph_id
            LEFT JOIN(
                    SELECT DISTINCT
                            a.ad_id ,
                            count(*) AS ad_count
                    FROM
                            ad a ,
                            ad_fleet f ,
                            ad_paragraph b, paragraph_option d
                    WHERE
                            a.ad_id = b.ad_id $actionFilter
                    AND a.ad_id = f.ad_id
                    AND f.fleet_id IN($fleets)
                    AND b.ad_paragraph_id = d.ad_paragraph_id
                    AND a.deleted = 0
                    AND f.deleted = 0
                    AND b.deleted = 0
                    AND d.deleted = 0
                    GROUP BY
                            a.ad_id ,
                            f.fleet_id
            ) f ON a.ad_id = f.ad_id
            AND a.deleted = 0 ,
            (
                    SELECT DISTINCT
                            ad_id
                    FROM
                            ad_fleet
                    WHERE
                            fleet_id IN($fleets)
                    AND deleted = 0
            ) e
            WHERE
                    a.ad_id = e.ad_id 
            AND a.deleted = 0
            ORDER by a.ad_txt, ad_paragraph_id, paragraph_option_id";
        //echo $sql;exit;
        $query = $this->getAdapter()->quoteInto($sql, '');
        return $this->getAdapter()->fetchAll($query);
    }

}
