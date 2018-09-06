<?php

error_reporting(0);

class ICAparsers {
    public function parseRequirements($id, $string){
        $text = strtoupper( str_replace(array('.',','), '', $string) ); // sometimes source has commas and periods where they don't belong e.g., 7,400 FH

        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(?<![A-Za-z.])A{1}\b)~", $text, $Acheck); // only capture A, not AH //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(?<![A-Za-z.])B{1}\b)~", $text, $Bcheck);
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(?<![A-Za-z.])C{1}\b)~", $text, $Ccheck);
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(?<![A-Za-z.])D{1}\b)~", $text, $Dcheck);

        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>ACT\sFC)~", $text, $act_fc); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>ACT\sMO)~", $text, $act_mo); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>ACT\sYE)~", $text, $act_ye); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>AH)~", $text, $ah); // APU hours //value

        preg_match("~(?:\h)?(?P<unit>(APU\sCHG\b)|(APU\sCNG\b))~", $text, $apu_chg); // returns unit

        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>CRR\sFH)~", $text, $crr_fh); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>CRR\sMO)~", $text, $crr_mo); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(DAYS\b)|(DY\b))~", $text, $dy); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>EACH\sBRAKE\sREPLACE)~", $text, $ebr); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(?<![A-Za-z.])EC\b)~", $text, $ec); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>EH)~", $text, $eh); //value

        preg_match("~(?:\h)?(?P<unit>(ENG\sCHG\b)|(ENG\sCNG\b))~", $text, $eng_chg); // returns unit

        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>FC)~", $text, $fc); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>FH)~", $text, $fh); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>HOURS)~", $text, $hours); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>(?<![A-Za-z.])HR\b)~", $text, $hr); //value

        preg_match("~(?:\h)?(?P<unit>(IDG\sCHG\b)|(IDG\sCNG\b))~", $text, $idg_chg); // returns unit
        preg_match("~(?:\h)?(?P<unit>(LDG\sCHG\b)|(LDG\sCNG\b))~", $text, $ldg_chg); // returns unit

        preg_match("~(?:\h)?(?P<unit>LIF\sLIM)~", $text, $lif_lim); // returns unit
        preg_match("~(?:(?P<value>[adAD]*?)(?:\h)*?)?(?P<unit>LTR\sCHK)~", $text, $ltr_chk); //value

        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>MO)~", $text, $mo); //value

        preg_match("~(?:\h)?(?P<unit>SHP\sVST)~", $text, $shp_vst); // returns unit
        preg_match("~(?:\h)?(?P<unit>(TRV\sCHG\b)|(TRV\sCNG\b))~", $text, $trv_chg); // returns unit
        preg_match("~(?:\h)?(?P<unit>VEN\sREC)~", $text, $ven_rec); // returns unit

        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>YE)~", $text, $ye); //value
        preg_match("~(?:(?P<value>\d+(?:\.\d+)*?)(?:\h)*?)?(?P<unit>YR)~", $text, $yr); //value

        preg_match("~((?P<unit>NOTE)(?:\s)?(?P<value>\d+))~", $text, $numbered_note); //value

        preg_match("~(?P<unit>NOTE)~", $text, $note); // returns unit

        preg_match("~(?P<unit>(\*))~", $text, $star_note);

        // var_dump($apu_chg);

        $error_novalue = "ERROR CODE 0";


        // $note will be set if a numbered_note is found. In this case, we don't need $note to be stored
        if($note && $numbered_note){
            $note = null;
        }
        return array(
            'id'            => $id,
            'A'             => (!empty($Acheck['unit']) && $Acheck['value'] == null ? 1 : 0) ?: (!empty($Acheck['value']) ? $Acheck['value'] : NULL),
            'B'             => (!empty($Bcheck['unit']) && $Bcheck['value'] == null ? 1 : 0) ?: (!empty($Bcheck['value']) ? $Bcheck['value'] : NULL),
            'C'             => (!empty($Ccheck['unit']) && $Ccheck['value'] == null ? 1 : 0) ?: (!empty($Ccheck['value']) ? $Ccheck['value'] : NULL),
            'D'             => (!empty($Dcheck['unit']) && $Dcheck['value'] == null ? 1 : 0) ?: (!empty($Dcheck['value']) ? $Dcheck['value'] : NULL),   
            'act_fc'        => (!empty($act_fc['unit']) && $act_fc['value'] == null ? $error_novalue : $act_fc['value']),
            'act_mo'        => (!empty($act_mo['unit']) && $act_mo['value'] == null ? $error_novalue : $act_mo['value']),
            'act_ye'        => (!empty($act_ye['unit']) && $act_ye['value'] == null ? $error_novalue : $act_ye['value']),
            'ah'            => (!empty($ah['unit']) && $ah['value'] == null ? $error_novalue : $ah['value']),
            'apu_chg'       => !empty($apu_chg['unit']) ? true : NULL,
            'crr_fh'        => (!empty($crr_fh['unit']) && $crr_fh['value'] == null ? $error_novalue : $crr_fh['value']),
            'crr_mo'        => (!empty($crr_mo['unit']) && $crr_mo['value'] == null ? $error_novalue : $crr_mo['value']),
            'dy'            => (!empty($dy['unit']) && $dy['value'] == null ? $error_novalue : $dy['value']),
            'ec'            => (!empty($ec['unit']) && $ec['value'] == null ? $error_novalue : $ec['value']),
            'eh'            => (!empty($eh['unit']) && $eh['value'] == null ? $error_novalue : $eh['value']),
            'eng_chg'       => !empty($eng_chg['unit']) ? true : NULL,
            'fc'            => (!empty($fc['unit']) && $fc['value'] == null ? $error_novalue : $fc['value']),
            'fh'            => (!empty($fh['unit']) && $fh['value'] == null ? $error_novalue : $fh['value']),
            'hr'            => (!empty($hr['unit']) && $hr['value'] == null ? $error_novalue : $hr['value']),
            'hours'         => (!empty($hours['unit']) && $hours['value'] == null ? $error_novalue : $hours['value']),
            'idg_chg'       => !empty($idg_chg['unit']) ? true : NULL,
            'ltr_chk'        => (!empty($ltr_chk['unit']) && $ltr_chk['value'] == null ? $error_novalue : $ltr_chk['value']),
            'ldg_chg'       => !empty($ldg_chg['unit']) ? true : NULL,
            'lif_lim'       => !empty($lif_lim['unit']) ? true : NULL,
            'mo'            => (!empty($mo['unit']) && $mo['value'] == null ? $error_novalue : $mo['value']),
            'note'          => !empty($note['unit']) ? true : NULL,
            'numbered_note' => (!empty($numbered_note['unit']) && $numbered_note['value'] == null ? $error_novalue : $numbered_note['value']),
            'shp_vst'       => !empty($shp_vst['unit']) ? true : NULL,
            'star_note'     => !empty($star_note['unit']) ? true : NULL,
            'trv_chg'       => !empty($trv_chg['unit']) ? true : NULL,
            'ven_rec'       => !empty($ven_rec['unit']) ? true : NULL,
            'ye'            => (!empty($ye['unit']) && $ye['value'] == null ? $error_novalue : $ye['value']),
            'yr'            => (!empty($yr['unit']) && $yr['value'] == null ? $error_novalue : $yr['value'])

        );
    }

    public function saveData($type = null, $method_id, $data, $document_id, $document_item_id)
    {
        $table = new Application_Model_DbTable_Intervals();
        foreach ($data as $key => $value) {
            $note = null;
            // skip some keys
            if($key == 'id') continue;
            if(is_null($value)) continue;
            if($key == 'note' && $value){
              $key = 'NOTE';
              $value = 'NOTE';
              $note = 'NOTE';
            }
            if($key == 'numbered_note' && $value){
              $key = 'NOTE';
              $value = $value;
              $note = 'NOTE ' . $value;
            }
            if($key == 'star_note' && $value){
              $key = '(*)';
              $value = '(*)';
              $note = '(*)';
            }
            // save the interval data
            $data = array(
              'document_id'        => $document_id,
              'document_item_id'   => $document_item_id,
              'interval_method_id' => $method_id,
              'interval_type'      => $key,
              'interval_value'     => $value,
              'interval_note'      => $note
            );
            $row_id = $table->insert($data);
        }
        return TRUE;
    }
}