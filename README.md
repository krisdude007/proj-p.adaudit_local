# p.mm_manager
Data Vault (Formerly MPD-MRB Manager)

=========
This application is a repository for:

1. MPD and MRB data from all manufacturers.
2. TCDS information.
3. AD information.
 
=========

##Change History
To include change history for updates and deletions to existing records, add these lines to the applicable `update` and `delete` methods

    $changeTable = new Application_Model_DbTable_Changehistory();
    $changeTable->changed(array(
	    'who'     => 'User: '.$this->user['user_full_nm']. ' ID: '. $this->user['user_id'],
	    'what'    => 'Document ID:'.$document_id . ' Record ID:'.$record_id . ' Deleted',
	    'when'    => date('Y/m/d H:i:s',time()),
	    'table'   => get_class($storageTable),
	    'user_id' => $this->user['user_id']
	    )
    );

## MPD/MRB Imports
To import data from MPD XLS give columns the following names

     ******** NEEDS TO BE UPDATED **************

	change_bar
	mpd_item_number
	amm_reference
	cat
	task
	int_threshold
	int_repeat
	zone
	access
	appl_airplane
	appl_engine
	man_hours
	task_description
	aircraft: 757 -or- 767 etc...
	revision: YYYY-MM-DD
	ata: enter Excel formula =left(B2:2) into first row and fill down
	group: Systems and Powerplant -or- Structural -or- Zonal

To import revision highlights give columns the following names

	section
	item
	description
	aircraft: 757 -or- 767 etc...
	revision: YYYY-MM-DD