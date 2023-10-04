<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class preferences extends ApplicationBase {

    private $arr_unit_child = array();

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_preferences');
        // load library
        $this->load->library('pagination');
        $this->load->library('tnotification');
    }

    // view
    public function index() {
        //set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/list.html");
        /* start of pagination --------------------- */
        // pagination
        $total_data = count($this->arr_unit_child);
        $config['base_url'] = site_url("pengaturan/preferences/index/");
        $config['total_rows'] = $this->m_preferences->get_total_preferences();
        $config['uri_segment'] = 4;
        $config['per_page'] = 50;
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
        // get list data
        $params = array(($start - 1), $config['per_page']);
        $this->smarty->assign("rs_preferences_all", $this->m_preferences->get_all_preferences());
        $this->smarty->assign("rs_preferences", $this->m_preferences->get_all_preferences_limit($params));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // get unit by limit
    private function _get_arr_unit_by_limit($params) {
        $arr_list_data = array();
        $i = $params[0];
        while ($i < ($params[0] + $params[1])) {
            if (isset($this->arr_unit_child[$i])) {
                $arr_list_data[] = $this->arr_unit_child[$i];
            }
            $i++;
        }
        return $arr_list_data;
    }

    //add
    public function add() {
        //set rule
        $this->_set_page_rule("C");
        $this->com_user['user_id'];
        //set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/add.html");
//        $this->_get_unit_by_parent();
        $this->smarty->assign("arr_unit", $this->arr_unit_child);
        //get list jurusan
        $this->smarty->assign("rs_preferences", $this->m_preferences->get_all_preferences());
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    //add process
    public function process_add() {
        //set rule
        $this->_set_page_rule("C");
        //cek input
        $this->tnotification->set_rules('pref_group', 'Group', 'trim|required');
        $this->tnotification->set_rules('pref_nm', 'Name', 'trim|required');
        $this->tnotification->set_rules('pref_value', 'Value', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('pref_group'),
                $this->input->post('pref_nm'),
                $this->input->post('pref_value'),
                $this->com_user['user_id']
            );
            //insert
            if ($this->m_preferences->insert($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        //default redirect
        redirect("pengaturan/preferences/add");
    }

    //hapus
    public function delete($id = "") {
        //set rule
        $this->_set_page_rule("D");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/delete.html");
        //get detail data
        $this->smarty->assign("result", $this->m_preferences->get_preferences_by_id($id));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    //process hapus
    public function process_delete() {
        //set rule
        $this->_set_page_rule("D");
        //cek input
        $this->tnotification->set_rules('pref_id', 'ID', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            if ($this->m_preferences->delete($this->input->post('pref_id'))) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
                redirect("pengaturan/preferences");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        //default redirect
        redirect("pengaturan/preferences/delete/");
    }

    //edit
    public function edit($id = "") {
        //set rule
        $this->_set_page_rule("U");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/edit.html");
        //get detail data
        $this->smarty->assign("rs_preferences", $this->m_preferences->get_preferences_by_id($id));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    public function process_edit() {
        //set rule
        $this->_set_page_rule("U");
        //cek input
        $this->tnotification->set_rules('pref_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('pref_group', 'Group', 'trim|required');
        $this->tnotification->set_rules('pref_nm', 'Name', 'trim|required');
        $this->tnotification->set_rules('pref_value', 'Value', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('pref_group'),
                $this->input->post('pref_nm'),
                $this->input->post('pref_value'),
                $this->input->post('mdb'),
                $this->input->post('pref_id')
            );
            //insert
            if ($this->m_preferences->update($params)) {
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
        redirect("pengaturan/preferences/edit/" . $this->input->post('pref_id', 0));
    }

    //proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("sess_arr_search");
        } else {
            $params = array(
                "pref_group" => $this->input->post("pref_group")
            );
            $this->tsession->set_userdata("sess_arr_search", $params);
        }
        // redirect
        redirect("pengaturan/preferences" . $this->input->post("pref_id", 0));
    }

    //reset cari
    public function reset_cari() {
        $this->tsession->unset_userdata("ses_arr_search");
        redirect("pengaturan/preferences");
    }

}
