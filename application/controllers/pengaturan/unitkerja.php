<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class UnitKerja extends ApplicationBase {

    private $arr_unit_child = array();

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_unitkerja');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/unitkerja/list.html");
        //get data
        $this->_get_unit_by_parent();
        $total_data = count($this->arr_unit_child);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/unitkerja/index/");
        $config['total_rows'] = (!empty($total_data)) ? count($this->arr_unit_child) : "0";
        $config['uri_segment'] = 4;
        $config['per_page'] = 20;
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
        // get list
        $params = array(($start - 1), $config['per_page']);
        $this->smarty->assign("rs_unit", $this->_get_arr_unit_by_limit($params));
        // notification
        $this->tnotification->display_notification();
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

    // get unit by parent
    private function _get_unit_by_parent($parent_id = "0", $indent = "") {
        $arr_unit = $this->m_unitkerja->get_all_unit_by_parent($parent_id);
        if (!empty($arr_unit)) {
            $indent .= " ---";
            foreach ($arr_unit as $rs_unit) {
                $rs_unit['indent'] = $indent;
                $this->arr_unit_child[] = $rs_unit;
                $arr_child = $this->_get_unit_by_parent($rs_unit['unit_id'], $indent);
                if (!empty($arr_child)) {
                    $this->arr_unit_child[] = $arr_child;
                }
            }
        }
    }

    // add unit
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/unitkerja/add.html");
        // get data
        $this->smarty->assign("rs_unit", $this->m_unitkerja->get_all_unit());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('unit_cd', 'Kode Unit', 'trim|required|maxlength[30]');
        $this->tnotification->set_rules('unit_parent', 'Induk Unit', 'trim');
        $this->tnotification->set_rules('unit_name', 'Nama Unit', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('unit_lead', 'Kepala Unit', 'trim|required|maxlength[100]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('unit_cd'),
                $this->input->post('unit_parent'),
                $this->input->post('unit_name'),
                $this->input->post('unit_lead'),
                $this->com_user['user_id']);
            // insert
            if ($this->m_unitkerja->insert($params)) {
                // success
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
        redirect("pengaturan/unitkerja/add/");
    }

    // edit unit
    public function edit($unit_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/unitkerja/edit.html");
        // get data
        $unit = $this->m_unitkerja->get_unit_by_id($unit_id);
        $this->smarty->assign("result", $unit);
        $this->smarty->assign("rs_unit", $this->m_unitkerja->get_all_unit());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('unit_id', 'ID Unit', 'trim|required');
        $this->tnotification->set_rules('unit_cd', 'Kode Unit', 'trim|required|maxlength[30]');
        $this->tnotification->set_rules('unit_parent', 'Induk Unit', 'trim');
        $this->tnotification->set_rules('unit_name', 'Nama Unit', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('unit_lead', 'Kepala Unit', 'trim|required|maxlength[100]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('unit_cd'),
                $this->input->post('unit_parent'),
                $this->input->post('unit_name'),
                $this->input->post('unit_lead'),
                $this->com_user['user_id'],
                $this->input->post('unit_id'));
            if ($this->m_unitkerja->update($params)) {
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                redirect("pengaturan/unitkerja/edit/" . $this->input->post('unit_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/unitkerja/edit/" . $this->input->post('unit_id'));
    }

    // hapus unit
    public function delete($unit_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/unitkerja/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_unitkerja->get_unit_by_id($unit_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // hapus process
    public function delete_process() {
        // set page rules
        $this->_set_page_rule("D");
        // cek input
        $this->tnotification->set_rules('unit_id', 'Region ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('unit_id'));
            // insert
            if ($this->m_unitkerja->delete($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/unitkerja");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/unitkerja/delete/" . $this->input->post('unit_id'));
    }

}
