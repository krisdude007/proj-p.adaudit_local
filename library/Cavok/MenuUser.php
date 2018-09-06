<?php

class Cavok_Menu_User
{
    public function get($user_id)
    {
        return '
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#"><i class="fa fa-envelope-o"></i> Messages</a></li>
            <li><a href="#"><i class="fa fa-server"></i> Tasks</a></li>
            <li><a href="#"><i class="fa fa-user"></i> Update Profile</a></li>
            <li><a href="#"><i class="fa fa-lock"></i> Change Password</a></li>
            <li><a href="https://cavokgroup.atlassian.net/servicedesk/customer/portal/3" target="_blank"><i class="fa fa-life-ring"></i> Support</a></li>
          </ul>
        </li>
        ';
    }
}