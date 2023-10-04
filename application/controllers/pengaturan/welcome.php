<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class welcome extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/welcome/index.html");

        // output
        parent::display();
    }

}