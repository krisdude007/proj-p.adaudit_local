<?php
class MenuController extends Zend_Controller_Action{

    public $menuTable;

    public function init(){
        $this->user = Zend_Registry::get('user');
        $this->user_role = Zend_Registry::get('user_role');
        $this->view->my_role = $this->user_role['application_role_nm'];
        date_default_timezone_set('America/Chicago');
        $this->app = Zend_Registry::get('app');
        $this->menuTable = new Application_Model_DbTable_Menu();       
    }

    public function indexAction()
    {
    }

    public function createAction()
    {
        
    }

    public function editAction()
    {
        
    }

    /**
     * Return the menu tree which is formatted for JSTree
     * @return json Menu tree object
     */
    public function getAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $menu_items = $this->menuTable->menus($this->app['application_id']);
        $menuArray = $this->menuTable->renderData($menu_items);
        echo json_encode($menuArray);
    }

    /**
     * Return the data for the selected menu item
     * @return json Menu item data
     */
    public function itemAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id', null);
        $item = $this->menuTable->item($id);

        $row['message'] = 'ok';
        $row['data'] = $item;
        $row['data']['access_options'] = array('user','manager','administrator');
        echo json_encode($row);
    }

    /**
     * Update the menu item
     * @return json Return an ok message or the exception string.
     */
    public function updateAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $data       = $this->_request->getParam('data', null);
        $selectname = $this->_request->getParam('selectname', null);

        $accessArray = array();
        $tempArray = array();

        // this foreach extracts data from the AJAX form post and puts it into arrays
        foreach ($data as $key=>$item) {
            if($item['name'] == 'id'){
                $id = $item['value']; // extract the id of the menu item that is being updated
            }
            if($item['name'] == $selectname){ 
                // extract the selected user access rights for the menu
                $accessArray[] = $item['value'];
            }else{
                // this is one of the other form elements
                $tempArray[$item['name']] = $item['value'];
            }
        }

        $access = implode(',', $accessArray); // turn the access rights into a comma delimited string

        $where = $this->menuTable->getAdapter()->quoteInto('id = ?', $id);
        try{
            $this->menuTable->update( array(
                'text'        => $tempArray['text'],
                'icon'        => $tempArray['icon'],
                'controller'  => $tempArray['controller'],
                'action'      => $tempArray['action'],
                'access'      => $access,
                'updt_usr_id' => $this->user['user_id'],
                'updt_dtm'    => date('Y/m/d H:i:s',time()),
                )
            , $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = $e->getMessage();
        }
        echo json_encode($row);
    }

    public function reorderAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_request->getParam('data', null);

        $id = $data['id'];
        $item = $this->menuTable->item($id);

        $parent = $data['parent'];
        $parent_id = ($parent == '#') ? 0 : $parent;
        $position = $data['position'];
        $itemList = $this->menuTable->menusByParent($this->app['application_id'], $parent_id);
        
        // var_dump($data);
        // var_dump($itemList);
        
        $newOrder = $this->menuTable->arrayInsert($itemList, $item, $position);
        $result = $this->menuTable->saveNewOrder($this->app['application_id'], $newOrder, $parent_id, $this->user['user_id'] );

        // array_splice($items, $position, 0, $item);
        // var_dump('-------- NEW -----------');
        // var_dump($newOrder);



        exit;
        $where = $this->menuTable->getAdapter()->quoteInto('id = ?', $data['id']);
        try{
            $this->menuTable->update( array(
                'parent_id'   => $data['parent'],
                'menu_order'  => $data['position'],
                'updt_usr_id' => $this->user['user_id'],
                'updt_dtm'    => date('Y/m/d H:i:s',time()),
                )
            , $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = $e->getMessage();
        }
        echo json_encode($row);

    }
    public function showAction()
    {

    }

    public function storeAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_request->getParam('data', null);
        $selectname = $this->_request->getParam('selectname', null);
        $accessArray = array();
        // this foreach extracts data from the AJAX form post and puts it into arrays
        foreach ($data as $key=>$item) {
            if($item['name'] == 'id'){
                $id = $item['value']; // extract the id of the menu item that is being updated
            }
            if($item['name'] == $selectname){ 
                // extract the selected user access rights for the menu
                $accessArray[] = $item['value'];
            }else{
                // this is one of the other form elements
                $tempArray[$item['name']] = $item['value'];
            }
        }

        $access = implode(',', $accessArray); // turn the access rights into a comma delimited string

        try{
            $new_row = $this->menuTable->createRow();
            $new_row->app_id      = $this->app['application_id'];
            $new_row->parent_id   = 0;
            $new_row->order       = 0;
            $new_row->text        = $tempArray['text'];
            $new_row->icon        = $tempArray['icon'];
            $new_row->controller  = $tempArray['controller'];
            $new_row->action      = $tempArray['action'];
            $new_row->access      = $access;
            $new_row->updt_usr_id = $this->user['user_id'];
            $new_row->updt_dtm    = date('Y/m/d H:i:s',time());
            $id = $new_row->save();
            $row['message'] = 'ok';
            $row['id'] = $id;
         }
        catch (Exception $e){
            $row['message'] = $e->getMessage();
         }
        echo json_encode($row); 
        // $this->_redirect('/manufacturer/index');
    }

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_request->getParam('id',0);
        // var_dump($id);exit;
        try{
            $where = $this->menuTable->getAdapter()->quoteInto('id = ?', $id);
            $this->menuTable->update(array('deleted'=>1), $where);
            $row['message'] = 'ok';
        }
        catch (Exception $e){
            $row['message'] = 'Delete function not yet implemented.';
        }
        echo json_encode($row);
    }
}