<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class Jabatan extends ApplicationBase {

    private $arr_jabs_child = array();

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_jabatan');
        // load library
        $this->load->library('pagination');
        $this->load->library('tnotification');
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
    }

    // view
    public function index() {
        //set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/jabatan/list.html");
        //get data
        $this->_get_jabs_by_parent();
        $total_data = count($this->arr_jabs_child);
        // pagination
        $config['base_url'] = site_url("pengaturan/jabatan/index/");
        $config['total_rows'] = (!empty($total_data)) ? count($this->arr_jabs_child) : "0";
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
        $this->smarty->assign("rs_jabatan", $this->_get_arr_jabs_by_limit($params));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // get jabatan by limit
    private function _get_arr_jabs_by_limit($params) {
        $arr_list_data = array();
        $i = $params[0];
        while ($i < ($params[0] + $params[1])) {
            if (isset($this->arr_jabs_child[$i])) {
                $arr_list_data[] = $this->arr_jabs_child[$i];
            }
            $i++;
        }
        return $arr_list_data;
    }

    // get jabatan by parent
    private function _get_jabs_by_parent($parent_id = "0", $indent = "") {
        $arr_jabs = $this->m_jabatan->get_all_jabatan_by_parent($parent_id);
        if (!empty($arr_jabs)) {
            $indent .= " ---";
            foreach ($arr_jabs as $rs_jabatan) {
                $rs_jabatan['indent'] = $indent;
                $this->arr_jabs_child[] = $rs_jabatan;
                $arr_child = $this->_get_jabs_by_parent($rs_jabatan['jabatan_id'], $indent);
                if (!empty($arr_child)) {
                    $this->arr_jabs_child[] = $arr_child;
                }
            }
        }
    }

    //add
    public function add() {
        //set rule
        $this->_set_page_rule("C");
        $this->com_user['user_id'];
        //set template content
        $this->smarty->assign("template_content", "pengaturan/jabatan/add.html");
        //untuk mengambil jabatan berdasarkan parent
        $this->_get_jabs_by_parent();
        $this->smarty->assign("arr_jabatan", $this->arr_jabs_child);
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    //add process
    public function add_process() {
        //set rule
        $this->_set_page_rule("C");
        //cek input
        $this->tnotification->set_rules('jabatan_parent', 'Induk Jabatan', 'trim');
        $this->tnotification->set_rules('jabatan_nama', 'Jabatan', 'trim|required|maxlength[100]');
        //process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('jabatan_parent'),
                $this->input->post('jabatan_nama'),
                $this->com_user['user_id']
            );
            //insert
            if ($this->m_jabatan->insert_jabatan($params)) {
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
        redirect("pengaturan/jabatan/add");
    }

    // delete
    public function delete($id = "") {
        //set rule
        $this->_set_page_rule("D");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/jabatan/delete.html");
        //get detail data
        $result = $this->m_jabatan->get_jabatan_by_id($id);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("jabatan_parent", $this->m_jabatan->get_jabatan_by_id($result['jabatan_parent']));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // delete process
    public function delete_process() {
        //set rule
        $this->_set_page_rule("D");
        //cek input
        $this->tnotification->set_rules('jabatan_id', 'Jabatan ID', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            if ($this->m_jabatan->delete_jabatan($this->input->post('jabatan_id'))) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
                redirect("pengaturan/jabatan");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        //default redirect
        redirect("pengaturan/jabatan/delete/");
    }

    //edit
    public function edit($id = "") {
        //set rule
        $this->_set_page_rule("U");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/jabatan/edit.html");
        //get detail data
        $this->_get_jabs_by_parent();
        $this->smarty->assign("arr_jabatan", $this->arr_jabs_child);
        $this->smarty->assign("result", $this->m_jabatan->get_jabatan_by_id($id));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // edit process
    public function edit_process() {
        //set rule
        $this->_set_page_rule("U");
        //cek input
        $this->tnotification->set_rules('jabatan_id', 'Jabatan ID', 'trim|required');
        $this->tnotification->set_rules('jabatan_parent', 'Induk Jabatan', 'trim');
        $this->tnotification->set_rules('jabatan_nama', 'Jabatan', 'trim|required|maxlength[100]');
        //process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('jabatan_parent'),
                $this->input->post('jabatan_nama'),
                $this->input->post('mdb'),
                $this->input->post('jabatan_id')
            );
            //insert
            if ($this->m_jabatan->update_jabatan($params)) {
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
        redirect("pengaturan/jabatan/edit/" . $this->input->post('jabatan_id', 0));
    }

}
