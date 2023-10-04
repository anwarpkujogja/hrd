<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class instansi extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_instansi');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/instansi/list.html");
        // pagination
        $config['base_url'] = site_url("pengaturan/instansi/index/");
                // search
        $search = $this->tsession->userdata("instansi_search");
        // search parameters
        $instansi = empty($search['instansi_name']) ? '%' : '%' . $search['instansi_name'] . '%';
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        }
        $config['total_rows'] = $this->m_instansi->get_total_instansi($instansi);
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
        $this->smarty->assign("rs_id", $this->m_instansi->get_all_instansi($params));
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }
        // searching
    public function proses_cari() {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("instansi_search");
        } else {
            $params = array(
                "instansi_name" => $this->input->post("instansi_name")
            );
            $this->tsession->set_userdata("instansi_search", $params);
        }
        // redirect
        redirect("pengaturan/instansi");
    }
    // add data
    public function add() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/instansi/add.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('instansi_name', 'Nama Instansi', 'trim|required');
        $this->tnotification->set_rules('instansi_alamat', 'Alamat Instansi', 'trim');
        $this->tnotification->set_rules('instansi_alamat', 'Alamat Instansi', 'trim');
        $this->tnotification->set_rules('instansi_kode', 'Kode Instansi', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('instansi_kode'),
                $this->input->post('instansi_name'),
                $this->input->post('instansi_telp'),
                $this->input->post('instansi_fax'),
                $this->input->post('instansi_alamat'),
                $this->com_user['user_id']);
            // insert
            if ($this->m_instansi->insert($params)) {
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
        redirect("pengaturan/instansi/add");
    }
    // edit data
    public function edit($instansi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/instansi/edit.html");
        // detail
        $result = $this->m_instansi->get_instansi_by_id($instansi_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
    // edit process
    public function edit_process() {
        // cek input
        
        $this->tnotification->set_rules('instansi_id', 'ID Instansi', 'trim|required');
        $this->tnotification->set_rules('instansi_name', 'Nama Instansi', 'trim|required');
        $this->tnotification->set_rules('instansi_alamat', 'Alamat Instansi', 'trim');
        $this->tnotification->set_rules('instansi_kode', 'Kode Instansi', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('instansi_kode'),
                $this->input->post('instansi_name'),
                $this->input->post('instansi_telp'),
                $this->input->post('instansi_fax'),
                $this->input->post('instansi_alamat'),
                $this->com_user['user_id'],
                $this->input->post('instansi_id'),
                );
                        
            // insert
            if ($this->m_instansi->update($params)) {
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
        redirect("pengaturan/instansi/edit/" .$this->input->post('instansi_id'));
    }
    // delete data
    public function delete($instansi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/instansi/delete.html");
        // detail
        $result = $this->m_instansi->get_instansi_by_id($instansi_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
    // delete process
    public function delete_process() {
        // cek input
        
        $this->tnotification->set_rules('instansi_id', 'ID Instansi', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
                        
            // insert
            if ($this->m_instansi->delete($this->input->post('instansi_id'))) {
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
        redirect("pengaturan/instansi");
    }
}
