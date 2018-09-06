<?php

//namespace ZFBootstrap\View\Helper\Navigation;

//use DOMDocument,
//    DOMXPath,
//    Zend_Navigation_Container,
//    Zend_View_Helper_Navigation_Menu;

/**
 * Adds support for the Twitter Bootstrap dropdown menus Javascript plugin
 * to the Zend_View_Helper_Navigation_Menu class.
 *
 * @author Michael Moussa <michael.moussa@gmail.com>
 */
class Bootstrap_View_Helper_Menu extends Zend_View_Helper_Navigation_Menu
{
    /**
     * Intercept renderMenu() call and apply custom Twitter Bootstrap class/id
     * attributes.
     *
     * @see   Zend_View_Helper_Navigation_Menu::renderMenu()
     * @param Zend_Navigation_Container $container (Optional) The navigation container.
     * @param array                     $options   (Optional) Options for controlling rendering.
     *
     * @return string
     */
    public function renderMenu(Zend_Navigation_Container $container = null, array $options = array())
    {
        return $this->applyBootstrapClassesAndIds(parent::renderMenu($container, $options));
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Applies the custom Twitter Bootstrap dropdown class/id attributes where
     * necessary.
     *
     * @param  string $html The HTML
     * @return string
     */
    protected function applyBootstrapClassesAndIds($html)
    {
        $domDoc = new DOMDocument('1.0', 'utf-8');
        $domDoc->loadXML('<?xml version="1.0" encoding="utf-8"?>' . $html);

        $xpath = new DOMXPath($domDoc);

        foreach ($xpath->query('//a[starts-with(@href, "#")]') as $item)
        {
            $result = $xpath->query('../ul', $item);

            if ($result->length === 1)
            {

                // Set the class for the top-level menu item
                $ul = $result->item(0);
                $ul->setAttribute('class', 'dropdown-menu');

    // var_dump($ul->getAttribute('class'));
if($ul->getAttribute('class') == 'scrollable-menu'){
}
                // Set the ID for the top-level menu item
                $li = $item->parentNode;
                // $li->setAttribute('id', substr($item->getAttribute('href'), 1));
// var_dump($li->getAttribute('class'));
                 $lp = $item->parentNode;               
// var_dump($item->getAttribute('class'));

                // Add class 'dropdown' if not already set
                if (($existingClass = $li->getAttribute('class')) !== '')
                {
                    $li->setAttribute('class', $existingClass . ' dropdown');
                }
                else
                {
                    $li->setAttribute('class', 'dropdown');
                }

                // Add the data-toggle=dropdown that the Bootstrap JS needs
                $item->setAttribute('data-toggle', 'dropdown');

                // if the menu item has a class of 'dropdownsubmen'
                // then set the containing li to 'dropdown-submenu'
                // In application.ini set the class
                // 
                //      menu.reports.pages.leveloneexport.label = Level Data Menu Group
                //      menu.reports.pages.leveloneexport.class = dropdownsubmenu
                //      menu.reports.pages.leveloneexport.uri = #
                //      
                if($item->getAttribute('class') == 'dropdownsubmenu'){
                    $li->setAttribute('class', 'dropdown-submenu');
                }
                // If the menu list is long and needs to scroll then add a 
                // class of 'scrollablemenu' to the top-level item in
                // application.ini
                // 
                // menu.lookup.label = Maintenance
                // menu.lookup.uri = #
                // menu.lookup.resource = menu
                // menu.lookup.privilege = admin
                // menu.lookup.params_id = "PROJECT_ID"
                // menu.lookup.class = "scrollablemenu"
                // 
                if($item->getAttribute('class') == 'scrollablemenu'){
                    $ul->setAttribute('class', 'scrollable-menu dropdown-menu');
                }
                
                // Add class dropdown-toggle if not already set
                if (($existingClass = $item->getAttribute('class')) !== '')
                {
                    $item->setAttribute('class', $item->getAttribute('class') . ' dropdown-toggle');
                }
                else
                {
                    $item->setAttribute('class', 'dropdown-toggle');
                }
                
                $space = $domDoc->createTextNode(' ');

                $item->appendChild($space);

                $caret = $domDoc->createElement('b', '');
                // $caret->setAttribute('class', 'caret');

                $item->appendChild($caret);
            }
        }
        return $domDoc->saveXML($xpath->query('/ul')->item(0), LIBXML_NOEMPTYTAG);
    }
}
