<?php

class ReportsController extends Zend_Controller_Action {

    public $reportModel;

    public function init() {
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->reportModel = new Application_Model_Report();
    }

    public function indexAction() {
        $adImport = new Application_Model_DbTable_AdImport();
        $recs = $adImport->getRecs();

        $fleetArr = array();
        foreach ($recs as $r) {
            $currFleet = explode(', ', trim($r['ad_fleet']));
            $fleetArr = array_merge($fleetArr, $currFleet);
        }
        var_dump(array_unique($fleetArr));
    }

    public function finalreportAction() {

        $adId = isset($_SESSION['adId']) ? $_SESSION['adId'] : 0;
        $fleets = isset($_SESSION['fleets']) ? $_SESSION['fleets'] : 0;
        $filterBy = $this->_request->getParam('filterBy', null);

        if ($adId > 0) {
            $reportResult = $this->reportModel->getFinalReport($fleets, $filterBy);
            $this->view->reportResult = $reportResult;
        } else {
            $this->_redirect('/ad/index');
        }

        $this->view->filterBy = $filterBy;
    }

    public function exporttoexcelAction() {

        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $adId = isset($_SESSION['adId']) ? $_SESSION['adId'] : 0;
        $fleets = isset($_SESSION['fleets']) ? $_SESSION['fleets'] : 0;
        $filterBy = $this->_request->getParam('filterBy', null);

        if ($adId > 0) {
            $reportData = $this->reportModel->getFinalReport($fleets, $filterBy);
        } else {
            $reportData = "Error in report. Please contact your administrator";
        }
        $now = gmdate("D, d M Y H:i:s");

        header("Content-type: application/xlsx");
        header("Content-Disposition: attachment; filename=REPORT" . date("Ymd") . "-" . time() . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $report = '<table id="example" class="display table-striped table-bordered" border= "1" cellspacing="0" style="width: 90%; border: 1px solid #000000;" width="90%">
        <thead>
            <tr>
                <th bgcolor="silver">AD Number</th>
                <th bgcolor="silver">Paragraph</th>
                <th bgcolor="silver">If</th>
                <th bgcolor="silver">Options</th>
                <th bgcolor="silver">Do</th>
                <th bgcolor="silver">Limit</th>
                <th bgcolor="silver">From</th>
                <th bgcolor="silver">Or Sequence</th>
                <th bgcolor="silver">Exception</th>
            </tr>
        </thead>
        <tbody>';
        $curParaId = 0;
        $curAdId = 0;

        foreach ($reportData as $result) {
            $option_txt = !empty($result['option_txt']) ? $result['option_txt'] : '&nbsp;';
            $limit_txt = !empty($result['limit_txt']) ? $result['limit_txt'] : '&nbsp;';
            $from_txt = !empty($result['from_txt']) ? $result['from_txt'] : '&nbsp;';
            $or_txt = !empty($result['or_txt']) ? $result['or_txt'] : '&nbsp;';
            $exception_txt = !empty($result['exception_txt']) ? $result['exception_txt'] : '&nbsp;';
            $report .= '<tr valign="middle" >';
            if ($curAdId != $result['ad_id']) {
                $report.= '<td rowspan="' . $result['ad_count'] . '">' . '="' . $result['ad_txt'] . '"</td>';
            }
            if ($curParaId != $result['ad_paragraph_id'] || $curAdId != $result['ad_id']) {
                $report.= '<td rowspan="' . $result['option_count'] . '">' . $result['paragraph_txt'] . '</td>';
            }
            if ($curParaId != $result['ad_paragraph_id'] || $curAdId != $result['ad_id']) {
                $report.= '<td rowspan="' . $result['option_count'] . '">' . $result['if_txt'] . '</td>';
            }
            $report .= '<td>="' . $option_txt . '"</td>';
            if ($curParaId != $result['ad_paragraph_id'] || $curAdId != $result['ad_id']) {
                $report.= '<td rowspan="' . $result['option_count'] . '">' . $result['do_txt'] . '</td>';
            }
            $curParaId = $result['ad_paragraph_id'];
            $curAdId = $result['ad_id'];
            $report .= '<td>' . $limit_txt . '</td>';
            $report .= '<td>="' . $from_txt . '"</td>';
            $report .= '<td>' . $or_txt . '</td>';
            $report .= '<td>' . $exception_txt . '</td>';

            $report .= '</tr>';
        }
        $report .= '</tbody>';
        $report .= '</table>';

        echo $report;
    }

    public function createfleetAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $adImport = new Application_Model_DbTable_AdImport();
        $fleetTable = new Application_Model_DbTable_Fleet();
        $recs = $adImport->getRecs();

        $fleetArr = array();
        foreach ($recs as $r) {
            $currFleet = explode(', ', trim($r['ad_fleet']));
            $fleetArr = array_merge($fleetArr, $currFleet);
        }
        $fleetArr = array_unique($fleetArr);

        try {
            foreach ($fleetArr as $fl) {
                $data = $fleetTable->insert([
                    'fleet_txt' => $fl,
                    'crea_usr_id' => 2675,
                    'crea_dtm' => date('Y/m/d H:i:s', time())
                ]);
            }
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }
    
    public function createparagraphAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $adImport = new Application_Model_DbTable_AdParaImport();
        $paragraphTable = new Application_Model_DbTable_AdParagraph();
        $paragraphOptionTable = new Application_Model_DbTable_ParagraphOption();
        $recs = $adImport->import();

        try {
            $curParaTxt = '';
            //$curAdId = 0;
            foreach ($recs as $r) {
                if ($curParaTxt != $r['paragraph_txt']){
                $data = array('ad_id' => $r['ad_id'],
                    'paragraph_txt' => trim($r['paragraph_txt']),
                    'if_txt' => trim($r['if_txt']),
                    'do_txt' => trim($r['do_txt']),
                    'crea_usr_id' => 2675,
                    'crea_dtm' => date('Y/m/d H:i:s', time())
                    );
                //var_dump($data);
                $paragh_id = $paragraphTable->insert($data);
                $curParaTxt = $r['paragraph_txt'];
                }
                
                $data1 = array('ad_paragraph_id' => $paragh_id,
                    'option_txt' => trim($r['option_txt']),
                    'from_txt' => trim($r['from_txt']),
                    'limit_txt' => trim($r['limit_txt']),
                    'or_txt' => trim($r['or_txt']),
                    'exception_txt' => trim($r['exception_txt']),
                    'crea_usr_id' => 2675,
                    'crea_dtm' => date('Y/m/d H:i:s', time())
                    );
                $paragh_option_id = $paragraphOptionTable->insert($data1);
            }
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    public function createadfleetAction() {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $adImport = new Application_Model_DbTable_AdImport();
        $fleetTable = new Application_Model_DbTable_Fleet();
        $adFleetTable = new Application_Model_DbTable_AdFleet();

        $recs = $adImport->import();
        //var_dump($recs);exit;
        foreach ($recs as $r) {
            $currFleet = explode(', ', trim($r['ad_fleet']));
            $currFleet = array_unique($currFleet);
            $currFleet = array_values($currFleet);
            //var_dump($currFleet);
            foreach ($currFleet as $c) {
                $fleetRec = $fleetTable->getFleetByFleetTxt($c);
                try {
                    $fleetId = $fleetRec[0]['fleet_id'];
                    $data = $adFleetTable->insert([
                        'ad_id' => $r['ad_id'],
                        'fleet_id' => $fleetId,
                        'crea_usr_id' => 2675,
                        'crea_dtm' => date('Y/m/d H:i:s', time())
                    ]);
                } catch (Exception $e) {
                    var_dump($e);
                    exit;
                }
            }
        }//exit;
    }

}
