<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // Our Standard Registry objects (Candidates for Zend Cache)
    protected $config           = null; // $this->config->(Option) = applicaiton.ini config option
    protected $db               = null; // Application DB
    protected $log              = null; // Logging object
    protected $app              = null; // Application specific configuration from shared db.
    protected $user             = null; // The user row from the user table in the shared db.
    protected $controller       = null;
    protected $action           = null;
    protected $menu             = null;

    // local only properties
    protected $Signed_in        = false; // No user signed in by default

    protected $AuthServer       = ''; // Authentication server, logins are redirected to this server.
    protected $LogInOutURL      = ''; // URL to login/logout of the applications.
    protected $ProfileURL       = ''; // URL for the user to modify their profile.

    /***********************************************************************
     * Force SSL for our production environment.
     **********************************************************************/
    protected function _initForceSSL() {
        // if we are command line then just return
        if (PHP_SAPI == 'cli')
            return;

        if ( APPLICATION_ENV == 'production' )
        {
            if($_SERVER['SERVER_PORT'] != '443') {
                header('Location: https://' . $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }

    protected function _initParams(){
        $url = $_SERVER["REQUEST_URI"];
        if (isset($url)) {
            $e = explode('/', $url);
            if (isset($e[1])) {
                $this->controller = $e[1];
            }
            if (isset($e[2])) {
                $this->action = $e[2];
            }
        }
    }

    /***********************************************************************
     * Get a copy of our configuration environment.
     **********************************************************************/
    protected function _initConfig()
    {
        $this->config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $this->config);
    }

    /***********************************************************************
     * Initialize our databases, setup two connections.
     *
     * The default connection will be used by our standard models in
     * /application/models/DbTables, and a second connection for our shared
     * modules in /application/models/Common.
     **********************************************************************/
    protected function _initDatabases()
    {
        // Load the multi db plugin
        $resource = $this->getPluginResource('multidb');
        $resource->init();

        // set our shared database connection used by the
        // models in the /application/models/Shared directory.
        Zend_Registry::set('shared_db', $resource->getDb('shared'));
    Zend_Registry::set('aircraft_db', $resource->getDb('aircraft'));
        // Setup our default database connection
        $this->bootstrap('db');
        $db = $this->getPluginResource('db');

        // force UTF-8 connection
        // $stmt = new Zend_Db_Statement_Pdo(
        //      $db->getDbAdapter(),
        //      "SET NAMES 'utf8'"
        // );
        // $stmt->execute();

        $dbAdapter = $db->getDbAdapter();

        // Query profiler (if enabled and not in production)
        $options = $db->getOptions();
        if ($options['profiler']['enabled'] == true
                && APPLICATION_ENV != 'production'
        ) {
            $profilerClass  = $options['profiler']['class'];
            $profiler       = new $profilerClass('All DB Queries');
            $profiler->setEnabled(true);
            $dbAdapter->setProfiler($profiler);
        }

        Zend_Registry::set('db', $dbAdapter);
    }

    /***********************************************************************
     * Initialize our logging.
     *
     * All logging more severe that "DEBUG" is sent to the log table of the
     * application database.  Firebug (FirePHP) is only enabled for
     * non production environments.
     *
     * log:
     * --------------------------------------------------------------------------------------------
     * | log_id  | message     | priority | timestamp  | priorityName | user_id     | request_uri |
     * --------------------------------------------------------------------------------------------
     * | Primary | Text string | Numeric  | Time error | String text  | user_id of  | URL of the  |
     * | Key     | of error    | priority | occurred   | of priority  | the user if | request if  |
     * |         | message.    |          |            |              | available.  | available.  |
     * --------------------------------------------------------------------------------------------
     **********************************************************************/
    protected function _initLogger() {

        // Setup logging
        $this->log = new Zend_Log();

        // Add user_id to the logged events
        $this->log->setEventItem('user_id', 0);
        // Add the URI to the logged events
        if (PHP_SAPI != 'cli')
            $this->log->setEventItem('request_uri', $_SERVER["REQUEST_URI"]);
        else
            $this->log->setEventItem('request_uri', 'command line');

        $writer_db = new Zend_Log_Writer_Db(Zend_Registry::get('db'), 'log');
        $this->log->addWriter($writer_db);

        // Prevent debug messages from going to the DB.
        $filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
        $writer_db->addFilter($filter);

        // if we are not in produciton enable Firebug
        // http://www.firephp.org/
        if ( APPLICATION_ENV != 'production' ) {
            $writer_firebug = new Zend_Log_Writer_Firebug();
            $this->log->addWriter($writer_firebug);
        }

        Zend_Registry::set( 'log', $this->log );

        // Examples:
        //Zend_Registry::get('log')->debug("this is a debug log test"); // least severe only shown on FireBug console
        //$this->log->debug("this is a debug log test");    // least severe only shown on FireBug console
        //Zend_Registry::get('log')->info("this is a info log test");
        //Zend_Registry::get('log')->notice("this is a notice log test");
        //Zend_Registry::get('log')->warn("this is a warn log test");
        //Zend_Registry::get('log')->err("this is a err log test");
        //Zend_Registry::get('log')->crit("this is a crit log test");
        //Zend_Registry::get('log')->alert("this is a alert log test");
        //Zend_Registry::get('log')->emerg("this is a emerg log test"); // Most severe
    }

    /***********************************************************************
     * Load application specific information from the shared databases.
     *
     * This is where we will override any applicaiton.ini configuration with
     * database driven configuration data.
     *
     * Required Table Structure (Table may contain more columns, but must
     * contain these):
     *
     * app:
     * -------------------------------------------------------------
     * | app_id           | app_nm      | app_sub_domain | deleted |
     * -------------------------------------------------------------
     * | Primary Key Must | Application | Sub-Domain for | Record  |
     * | Must match       | Name        | building URL   | Deleted |
     * | app_id in *.ini  |             |                |         |
     * -------------------------------------------------------------
     **********************************************************************/
    protected function _initApp()
    {
        date_default_timezone_set('America/Chicago');
        $this->log->debug($this->config->application_id);
        $application_model = new Application_Model_Shared_Application();
        $this->app = $application_model->find($this->config->application_id)->current();
        Zend_Registry::set('app', $this->app);
    }

    /***********************************************************************
     * Build the menu.
     **********************************************************************/
    protected function _initNavigation() {
        // if we are command line then just return
        if (PHP_SAPI == 'cli') return;

        $menuTable = new Application_Model_DbTable_Menu();
        $this->menu = $menuTable->buildAppMenu($this->app['application_id']);
    }

    /***********************************************************************
     * Load the ACL from the appliation.ini file.
     *
     * Format in applicaiton.ini is:
     * roles = (base role), (parent role):(child role), ..., administrator
     *
     * Example:
     * roles = view, user:view, admin:user, administrator
     *
     * view - The most basic role.
     * user - Can do anything view can + anything user can.
     * admin - Can do anything view + user + admin can.
     * administrator - Special role that can do anything.
     **********************************************************************/
    protected function _initACL() {
        //return;
        $this->acl = new Zend_Acl();

        $acls = explode(',',$this->config->roles);



        foreach($acls as $acl_pair) {
            $acl = explode(':', $acl_pair);

            if (isset($acl[1])) {
                $this->acl->addRole(new Zend_Acl_Role(trim($acl[0])),trim($acl[1]));
            }
            else {
                $this->acl->addRole(new Zend_Acl_Role(trim($acl[0])));
            }

            // our privileges match our roles.
            if (trim($acl[0])!='administrator')
                $this->acl->allow(trim($acl[0]), null, trim($acl[0]));
            else
                $this->acl->allow(trim($acl[0])); // Special role administrator can do anything!!!!
        }

        Zend_Registry::set('acl', $this->acl);
    }

    /***********************************************************************
     * Setup our login/logout URL and profile URL.  Allow for some other
     * server on the same domain to provide login services for multiple
     * applications.  This server could also use OAuth, Active Directory,
     * etc...
     *
     * As long as the proper cookies are setup to match the shared user table
     * the user will be "logged on".
     ***********************************************************************/
    protected function _initAuthServer() {

        if (PHP_SAPI != 'cli') {
            // If we have a login server use it to login else use our current server
            if (isset($this->config->authentication_subdomain)) {
                $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
                $this->AuthServer = "//".$this->getOption('authentication_subdomain').".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
            }
            else
                $this->AuthServer = $_SERVER["HTTP_HOST"];
        }

        $this->LogInOutURL = "".$this->AuthServer."/login";
        $this->ProfileURL  = "".$this->AuthServer."/login/reset"; // for now we just reset password.
    }

    /***********************************************************************
     * Make sure the user is logged in, and setup the user "object" for use
     * by the reset of the application.  This is who the user "is".  We have
     * three type of user logins.
     *
     * The first login type is command line where we use a command line user from
     * the config.  The second type is a cookied user, where the user has a
     * valid set of cookies that match the user table from the shared
     * database.  The final type of login in a HTTP BASIC Auth, used for
     * webservices.
     *
     * NOTE: This security is not hardened or tested.  Needs more research
     * and though.
     *
     * Required Table Structure (Table may contain more columns, but must
     * contain these):
     *
     * user:
     * -------------------------------------------------------------------------------------
     * | user_id     | user_nm    | password         | salt   | onetimepad       | deleted |
     * -------------------------------------------------------------------------------------
     * | Primary Key | user email | MD5(password_txt | Random | Key for cookies  | user    |
     * |             | address    | +salt)           | string | & password reset | deleted |
     * -------------------------------------------------------------------------------------
     **********************************************************************/
    protected function _initUser() {

        // User table
        $user_model = new Application_Model_Shared_User();

        // By default the User is not logged in
        $UserRow=false;

        // If we are command line attempt to use the command line user from
        // the config file.
        if (PHP_SAPI == 'cli') {
            if (isset($this->config->command_line_user))
                $UserRow = $user_model->find($this->config->command_line_user)->current();
            else {
                echo "\n\nNo Command line user set.\n\n";
                Zend_Registry::get('log')->emerg("Called from command line with no command line user set.");
                exit();
            }

        }

        // See if the user is logged in via cookies
        if (isset($_COOKIE['cavuser']) && isset($_COOKIE['cavpad']))
            $UserRow = $user_model->getUserByNameAndPad($_COOKIE['cavuser'],$_COOKIE['cavpad']);

        // See if the user is logged in via HTTP BASIC Auth, used mostly for Webservices
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_PW']) {
            $UserRow = $user_model->getUserByNameAndPassword($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
            if (!$UserRow)    // If we have HTTP BASIC Auth but could not sign in with a password try the pad
                $UserRow = $user_model->getUserByNameAndPad($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        }

        if ($UserRow) {
            if ($UserRow->deleted==0) {
                $this->user = $UserRow;
                $UserRow->password='';    // Obscure the passwords from the data set.
                $UserRow->salt='';        // Obscure the salt from the data set

                $this->Signed_in=true;

                // Set the user_id in the logger
                $this->log->setEventItem('user_id', $this->user->user_id);

                Zend_Registry::set('user', $UserRow);

                return;
            }
        }

        // We have not logged in the user so redirect the user to the login page.
        $this->user=null;
        Zend_Registry::set('user', null);

        // These are ok urls if we are not logged in.
        $login_urls=array('/login','/login/reset','/login/request','/login/forgot');
        if (isset($_SERVER['REDIRECT_URL']))
            if (in_array($_SERVER['REDIRECT_URL'],$login_urls))
                return; // we on on an allowed url,
                        // so return before we redirect to the login server.

        // Get our current_url
        $current_url=urlencode($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
        // https://atb.cavokgroup.com/login?ret=pm.cavokgroup.com%2F
        //echo ( "Location: http:".$this->LogInOutURL."?ret=".$current_url);
        //exit();
        // We were not authenticated and not on a login url so redirect to our login server.
        header( "Location: http:".$this->LogInOutURL."?ret=".$current_url);
        exit();
    }

    /***********************************************************************
     * Get the current user role, if there is no current user role will be
     * null.  This is what the user can do in the application.
     *
     * ----------------------------------------------------------------
     **********************************************************************/
    protected function _initRole() {
        if ($this->Signed_in) {
            $user_model = new Application_Model_Shared_UserApplication();
            $this->role = $user_model->getUserRoleByApplication($this->user['user_id'], $this->config->application_id);
             if ($this->role==null) {
                 // Error this user has no role for this applicaiton
                 echo('You don\'t have access to this application.<br>');
                 echo('Please contact the development team group for support.');
                 exit();

             }
            Zend_Registry::set('user_role', $this->role);
        }
    }

    /***********************************************************************
     * Populate the layout.  See resources.layout.layoutPath in *.ini file.
     **********************************************************************/
    protected function _initBuildLayout() {

        // if we are command line then just return
        if (PHP_SAPI == 'cli')
            return;

        // Bind our css for the layout to the view
        $this->bootstrap('layout');
        $this->layout = $this->getResource('layout');
        $this->view = $this->layout->getView();


        // *******************************************************
        // * Bootstrap front-end framwork
        // * http://getbootstrap.com/
        // *******************************************************
        $this->view->headScript()->appendFile('/js/jquery-2.1.4.min.js');
        $this->view->headScript()->appendFile('/bootstrap/js/bootstrap.min.js');
        $this->view->headLink()->appendStylesheet('/bootstrap/css/bootstrap.min.css','screen, print');

        // JQuery Stickytabs
        $this->view->headScript()->appendFile('/js/jquery-stickytabs.js');
        
        // Bootstrap Tables
        $this->view->headScript()->appendFile('/bootstrap-table/dist/bootstrap-table.min.js');
        $this->view->headScript()->appendFile('/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js');
        $this->view->headScript()->appendFile('/bootstrap-table/dist/extensions/export/jquery.tableexport.js');
        $this->view->headScript()->appendFile('/bootstrap-table/filesaver.min.js');
        $this->view->headScript()->appendFile('/bootstrap-table/bootstrap-table-export.js');
        $this->view->headScript()->appendFile('/bootstrap-table/bootstrap-table-cookie.js');
        $this->view->headScript()->appendFile('/bootstrap3-editable/js/bootstrap-editable.js');
        $this->view->headLink()->appendStylesheet('/bootstrap3-editable/css/bootstrap-editable.css','screen');
        $this->view->headLink()->appendStylesheet('/bootstrap-table/dist/bootstrap-table.min.css','screen');
        
        // Data tables
        $this->view->headScript()->appendFile('/datatables/datatables.min.js');
        $this->view->headScript()->appendFile('/datatables/datatables.bootstrap.min.js');
        $this->view->headScript()->appendFile('/datatables/jquery.datatables.min.js');
        /*********** new code for reports ******/
        
        $this->view->headScript()->appendFile('/datatables/Report/js/tableExport.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/jquery.base64.js');
        
        $this->view->headScript()->appendFile('/datatables/Report/js/dataTables.buttons.min.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/buttons.flash.min.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/jszip.min.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/pdfmake.min.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/vfs_fonts.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/buttons.html5.min.js');
        $this->view->headScript()->appendFile('/datatables/Report/js/buttons.print.min.js');
        
        /********** *********/
        $this->view->headLink()->appendStylesheet('/datatables/datatables.min.css','screen, print');
        $this->view->headLink()->appendStylesheet('/datatables/datatables.bootstrap.min.css','screen, print');
        $this->view->headLink()->appendStylesheet('/datatables/Report/css/buttons.dataTables.min.css','screen, print');
        $this->view->headLink()->appendStylesheet('/datatables/Report/css/jquery.dataTables.min.css','screen, print');
        
        // Jquery UI
        $this->view->headScript()->appendFile('/jquery-ui/jquery-ui.min.js');
        $this->view->headLink()->appendStylesheet('/jquery-ui/jquery-ui.min.css','screen, print');


        // JS Render
        $this->view->headScript()->appendFile('/jsviews/jsrender.min.js');
        
        // BS-3-Form
        $this->view->headScript()->appendFile('/js/cvk-bs3-form.js');

        // VueJS
        $this->view->headScript()->appendFile('/vuejs/vue.min.js');
        $this->view->headScript()->appendFile('/vuejs/vue-resource/dist/vue-resource.min.js');

        // Jstree
        $this->view->headScript()->appendFile('/jstree/dist/jstree.min.js');
        $this->view->headLink()->appendStylesheet('/jstree/dist/themes/default/style.min.css','screen, print');

        // Moment
        $this->view->headScript()->appendFile('/js/moment.js');

        // Bootstrap DateTime Picker
        // REF: http://eonasdan.github.io/bootstrap-datetimepicker/
        $this->view->headScript()->appendFile('/bootstrap-datetimepicker/bootstrap-datetimepicker.js');
        $this->view->headLink()->appendStylesheet('/bootstrap-datetimepicker/bootstrap-datetimepicker.css','screen, print');

        // Chosen
        $this->view->headScript()->appendFile('/chosen/chosen.jquery.min.js');
        $this->view->headLink()->appendStylesheet('/chosen/chosen.min.css','screen, print');

        // Populate the base css files
        $this->view->headLink()->appendStylesheet('/font-awesome/css/font-awesome.min.css','screen');
        $this->view->headLink()->appendStylesheet('/css/layout/body.css','screen');
        $this->view->headLink()->appendStylesheet('/css/cavok.css','screen');

        // Boostrap-dialog
        $this->view->headLink()->appendStylesheet('/bootstrap-dialog/dist/css/bootstrap-dialog.min.css','screen');
        $this->view->headScript()->appendFile('/bootstrap-dialog/dist/js/bootstrap-dialog.min.js');

        // SweetAlert
        $this->view->headLink()->appendStylesheet('/sweetalert/dist/sweetalert.css','screen');
        $this->view->headScript()->appendFile('/sweetalert/dist/sweetalert.min.js');

        // helpers
        $this->view->headScript()->appendFile('/js/nl2br.js');
        $this->view->headScript()->appendFile('/js/cvk-datavault-getters.js');
        $this->view->headScript()->appendFile('/js/doAjax.js');
        $this->view->headScript()->appendFile('/js/phpjs-master/functions/strings/htmlspecialchars_decode.js');

        // App specific JavaScript code
        $this->view->headScript()->appendFile('/js/app.js');

        // User info to the view
        $this->view->user = $this->user;
        $this->view->role = $this->role;
        $this->view->menu = $this->menu;

        if (isset($this->controller)) {
            $this->view->controller = $this->controller;
        }
        if (isset($this->action)) {
            $this->view->action = $this->action;
        }

        // set the default title from the config
        $this->view->app_name = $this->config->application_name;
        $this->view->app_icon = $this->config->application_icon;

        // set project name for title in page header
        $this->view->title = $this->projectName;

        // Links for the user toolbar.
        $this->view->LogInOutURL=$this->LogInOutURL;
        $this->view->ProfileURL=$this->ProfileURL;

        // Links for the footer.
        $this->view->copyright_company = $this->config->copyright_company;
        $this->view->copyright_link = $this->config->copyright_link;
    }

    /***********************************************************************
     * Build the Application menu.
     *********************************************************************/
    protected function _initAppMenu() {

        // if we are command line then just return
        if (PHP_SAPI == 'cli')
            return;

        if ($this->user) {
            $user_app = new Application_Model_Shared_UserApplication();

            // Get All Applications this user has access to
            $select = $user_app->select();
            $select->setIntegrityCheck(false);
            $select->from(array('user_application' => 'user_application'),array());
            $select->joinLeft('application','application.application_id=user_application.application_id');
            $select->where('user_application.user_id = ?',$this->user['user_id']);
            $select->order('application_nm');

            $apps = $user_app->fetchAll($select);

            $this->view->appMenu = array();
            $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
            foreach($apps as $key=>$app) {
                $this->view->appMenu[$key]['label']=$app['application_nm'];
                // Build out uri
                $this->view->appMenu[$key]['uri']="http://".$app['application_url'].".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
            }
        }
    }

    protected function _initCustomers() {

        // if we are command line then just return
        if (PHP_SAPI == 'cli')
            return;

        if ($this->Signed_in) {
            $customer_model = new Application_Model_Shared_Customer();
            // Get Clients
            $this->customers = $customer_model->getCustomerPairsByUserId($this->user['user_id']);
            if ($this->customers==null) {
                // Error this user has no client for this applicaiton
                echo('Well this is embarrassing.<br>');
                echo('Error: This user has no client for this applicaiton.<br>');
                echo('Please contact the applicaiton development group to fix this.');
                exit();
            }
            $this->view->header_customers = $this->customers;
            Zend_Registry::set('customers', $this->customers);
        }
    }

    protected function _initCurrentCustomer() {

        // if we are command line then just return
        if (PHP_SAPI == 'cli')
            return;

        if ($this->Signed_in) {

            $this->bootstrap = new Zend_Session_Namespace('bootstrap');

            $ValidCustomerIDs = array_keys($this->customers);
            $FirstCustomerID=$ValidCustomerIDs[0];

            // Set the client ID if it has not ben set
            if (!isset($this->bootstrap->current_customer)) {

                $this->bootstrap->current_customer=$this->customers[$FirstCustomerID];
                $this->bootstrap->current_customer=$FirstCustomerID;
            }

            // Yes, I know this is bad form, can make it a bitch to debug.  James
            if (isset($_POST['ClientSelectForm'])&&isset($_POST['ClientSelector'])) {
                if (in_array($_POST['ClientSelector'],$ValidCustomerIDs)) {
                    if ($this->bootstrap->current_customer!=$_POST['ClientSelector']) {    // If we have a customer selection change, the current contex becomes invalid
                        $this->bootstrap->current_customer=$_POST['ClientSelector'];
                        header('Location: /');                                       // so redirect to a context we know is valid, index/index.
                        die();
                    }
                }
            }
            $this->view->header_current_customer=$this->bootstrap->current_customer;

            $customer_model = new Application_Model_Shared_Customer();
            $CustomerRow = $customer_model->find($this->bootstrap->current_customer)->current();

            // Set our current customer
            Zend_Registry::set('current_customer', $CustomerRow);
        }
    }

}

