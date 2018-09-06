<?php

class Cavok_Menu
{
    public function get($menu, $user_access)
    {
        return $this->buildMenu($menu,$user_access);
    }

    private function buildMenu($menu_array,$user_access){
        $string = '<ul class="nav navbar-nav">';
        if (count($menu_array) > 0) {
            // First level menu
            foreach ($menu_array as $k=>$v) {
                $id         = $v['id'];
                $parent_id  = $v['parent_id'];
                $text       = $v['text'];
                $icon       = $v['icon'];
                $controller = $v['controller'];
                $action     = $v['action'];
                $access     = $v['access'];
                $children   = $v['children'];
                $url        = $this->menuURL($controller,$action);
                $text       = $this->menuText($icon, $text);
                // access control
                if(! in_array($user_access,  explode( ',', $access))) continue;

                // First submenu
                if (count($children) > 0) {
                    $string .= '<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$text.'<span class="caret"></span></a>
                        <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">';
                    foreach ($children as $sk=>$sv) {
                            $level_01_id         = $sv['id'];
                            $level_01_parent_id  = $sv['parent_id'];
                            $level_01_text       = $sv['text'];
                            $level_01_icon       = $sv['icon'];
                            $level_01_controller = $sv['controller'];
                            $level_01_action     = $sv['action'];
                            $level_01_access     = $sv['access'];
                            $level_01_children   = $sv['children'];
                            $level_01_url        = $this->menuURL($level_01_controller,$level_01_action);
                            $level_01_text       = $this->menuText($level_01_icon, $level_01_text);

                        // access control
                        if(! in_array($user_access,  explode( ',', $level_01_access))) continue;

                        // Second submenu
                        if (count($level_01_children) > 0) {
                                
                            $string .= '<li class="dropdown-submenu">
                                    <a tabindex="-1" href="'.$level_01_url.'">'.$level_01_text.'</a>
                                    <ul class="dropdown-menu">';
                            foreach ($level_01_children as $sck=>$scv) {
                                $level_02_id         = $scv['id'];
                                $level_02_parent_id  = $scv['parent_id'];
                                $level_02_text       = $scv['text'];
                                $level_02_icon       = $scv['icon'];
                                $level_02_controller = $scv['controller'];
                                $level_02_action     = $scv['action'];
                                $level_02_access     = $scv['access'];
                                $level_02_children   = $scv['children'];
                                $level_02_url        = $this->menuURL($level_02_controller,$level_02_action);
                                $level_02_text       = $this->menuText($level_02_icon, $level_02_text);

                                // access control
                                if(! in_array($user_access,  explode( ',', $level_02_access))) continue;

                                // Third submenu
                                if (count($level_02_children) > 0) {
                                  
                                    $string .='<li class="dropdown-submenu">
                                        <a tabindex="-1" href="'.$level_02_url.'">'.$level_02_text.'</a>
                                        <ul class="dropdown-menu">';
                                    foreach ($level_02_children as $ssck=>$sscv) {
                                        $level_03_id         = $sscv['id'];
                                        $level_03_parent_id  = $sscv['parent_id'];
                                        $level_03_text       = $sscv['text'];
                                        $level_03_icon       = $sscv['icon'];
                                        $level_03_controller = $sscv['controller'];
                                        $level_03_action     = $sscv['action'];
                                        $level_03_access     = $sscv['access'];
                                        $level_03_children   = $sscv['children'];
                                        $level_03_url        = $this->menuURL($level_03_controller,$level_03_action);
                                        $level_03_text       = $this->menuText($level_03_icon, $level_03_text);

                                        if(! in_array($user_access,  explode( ',', $level_03_access))) continue;

                                        $string .= '<li><a href="'.$level_03_url.'">'.$level_03_text.'</a></li>';
                                    }
                                        $string .= '</ul></li>';
                                } else {
                                    $string .='<li><a href="'. $level_02_url .'">'.$level_02_text.'</a></li>';
                                }
                            }
                            $string .='</ul></li>';
                        } else {
                           $string .= '<li><a href="'.$level_01_url.'">'.$level_01_text.'</a></li>';
                        }
                    }
                $string .= '</ul></li>';
            } else {
                $string .= '<li><a href="'.$url.''.$text.'</a></li>';
            }
        }
    }
    return $string . '</ul>';
}

    private function menuURL($controller, $action){
        if (isset($action)) {
            return '/'.$controller.'/'.$action;
        } else {
            return '/'.$controller;
        }
    }

    private function menuText($icon, $text){
        if (isset($icon)) {
            return '<i class="'.$icon.'"></i> '.$text;
        } else {
            return $text;
        }
    }
}