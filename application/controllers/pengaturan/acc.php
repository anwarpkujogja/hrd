<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class acc extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_peng_acc');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/acc/list.html");
        // pagination
        $config['base_url'] = site_url("pengaturan/instansi/index/");
                // search
        $search = $this->tsession->userdata("instansi_search");
        // search parameters
        $instansi = empty($search['instansi_name']) ? '%' : '%' . $search['instansi_name'] . '%';
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        }
        //$config['total_rows'] = $this->m_instansi->get_total_instansi($instansi);
        $config['uri_segment'] = 4;
        $config['per_page'] = 100;
        $this->pagination->initialize($config);
        $pagination['data'] = $this->pagination->create_links();
        // pagination attribute
        $start = $this->uri->segment(4, 0) + 1;
        $end = $this->uri->segment(4, 0) + $config['per_page'];
        $end = (($end > $config['total_rows']) ? $config['total_rows'] : $end);
        $pagination['start'] = ($config['total_rows'] == 0) ? 0 : $start;
        $pagination['end'] = $end;
        $pagination['total'] = $config['total_rows'];
        // pagination assign value
        $this->smarty->assign("pagination", $pagination);
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        $params = array($instansi, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_result", $this->m_peng_acc->get_all_bagian());
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }
    // edit data
    public function edit($FS_KD_BAGIAN = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/acc/edit.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // detail
        $this->smarty->assign("result", $this->m_peng_acc->get_bagian_by_id($FS_KD_BAGIAN));
        $this->smarty->assign("rs_unit", $this->m_peng_acc->get_unit_akuntansi());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
    // edit process
    public function edit_process() {
        // cek input
        
        $this->tnotification->set_rules('FS_KD_BAGIAN', 'Kode Bagian', 'trim|required');
        $this->tnotification->set_rules('FS_KD_UNIT', 'Kode Unit', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('FS_KD_UNIT'),
                $this->input->post('FS_KD_BAGIAN'),
                );
                        
            // insert
            if ($this->m_peng_acc->update($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/acc/");
    }
}
