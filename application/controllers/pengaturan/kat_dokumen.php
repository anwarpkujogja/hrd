<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class kat_dokumen extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_kat_dokumen');
        // load library
        $this->load->library('pagination');
        $this->load->library('tnotification');
    }

    // view
    public function index() {
        //set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/kat_dokumen/list.html");
        /* start of pagination --------------------- */
        // pagination
        $total_data = count($this->arr_unit_child);
        $config['base_url'] = site_url("pengaturan/kat_dokumen/index/");
        $config['total_rows'] = $this->m_kat_dokumen->get_total_kategori();
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
        $this->smarty->assign("rs_preferences", $this->m_kat_dokumen->get_all_kategori_limit($params));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    //add
    public function add() {
        //set rule
        $this->_set_page_rule("C");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/kat_dokumen/add.html");
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
        $this->tnotification->set_rules('kategori_nm', 'Nama Kategori', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('kategori_nm'),
                $this->com_user['user_id']
            );
            //insert
            if ($this->m_kat_dokumen->insert($params)) {
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
        redirect("pengaturan/kat_dokumen/add");
    }

    //hapus
    public function delete($id = "") {
        //set rule
        $this->_set_page_rule("D");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/kat_dokumen/delete.html");
        //get detail data
       $this->smarty->assign("result", $this->m_kat_dokumen->get_kategori_by_id($id));
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
        $this->tnotification->set_rules('kategori_id', 'ID', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            if ($this->m_kat_dokumen->delete($this->input->post('kategori_id'))) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
                redirect("pengaturan/kat_dokumen");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        //default redirect
        redirect("pengaturan/kat_dokumen/delete/");
    }

    //edit
    public function edit($id = "") {
        //set rule
        $this->_set_page_rule("U");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/kat_dokumen/edit.html");
        //get detail data
        $this->smarty->assign("rs_kategori", $this->m_kat_dokumen->get_kategori_by_id($id));
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
        $this->tnotification->set_rules('kategori_nm', 'Nama Kategori', 'trim|required');
        //process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('kategori_nm'),
                $this->com_user['user_id'],
                $this->input->post('kategori_id')
            );
            //insert
            if ($this->m_kat_dokumen->update($params)) {
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
        redirect("pengaturan/kat_dokumen/edit/" . $this->input->post('kategori_id'));
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
