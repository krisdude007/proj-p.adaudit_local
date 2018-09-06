<?php

class Cavok_Menu_System
{
    public function get($user_id)
    {
        $user_app = new Application_Model_Shared_UserApplication();

        // Get All Applications this user has access to
        $select = $user_app->select();
        $select->setIntegrityCheck(false);
        $select->from(array('user_application' => 'user_application'),array());
        $select->joinLeft('application','application.application_id=user_application.application_id');
        $select->where('user_application.user_id = ?', $user_id);
        $select->order('application_nm');

        $apps = $user_app->fetchAll($select);

        $split_hostname=explode(".", $_SERVER['SERVER_NAME']);

        $menuString = '<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog"></i> <span class="caret"></span></a>
          <ul class="dropdown-menu">';
            // foreach($apps as $key=>$app) {
            //     $this->view->appMenu[$key]['label']=$app['application_nm'];
            //     // Build out uri
            //     $this->view->appMenu[$key]['uri']="http://".$app['application_url'].".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
            // }

        foreach ($apps as $key => $value) {
           $menuString.= '<li><a href="http://';
           $menuString.= $value->application_url.'.'.$split_hostname[count($split_hostname)-2].'.'.$split_hostname[count($split_hostname)-1].'">';
           $menuString.= $value->application_nm."</a></li>";
        }
        $menuString .= '</ul></li>';
        return $menuString;
    }
}