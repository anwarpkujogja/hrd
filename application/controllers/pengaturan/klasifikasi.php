<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class klasifikasi extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_klasifikasi');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list klasifikasi
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/klasifikasi/list.html");
        // list kategori
        $this->smarty->assign("rs_kategori", $this->m_klasifikasi->get_list_kategori());
        // search
        $search = $this->tsession->userdata("klasifikasi_search");
        // search parameters
        $klas_id = empty($search['klas_id']) ? '%' : '%' . $search['klas_id'] . '%';
        $klasifikasi_cd = empty($search['klasifikasi_cd']) ? '%' : $search['klasifikasi_cd'] . '%';
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        }
        // pagination
        $config['base_url'] = site_url("pengaturan/klasifikasi/index/");
        $config['total_rows'] = $this->m_klasifikasi->get_total_klasifikasi(array($klas_id, $klasifikasi_cd));
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
        // get list data
        $params = array($klas_id, $klasifikasi_cd, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_klasifikasi->get_list_klasifikasi($params));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // searching
    public function proses_cari() {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("klasifikasi_search");
        } else {
            $params = array(
                "klas_id" => $this->input->post("klas_id"),
                "klasifikasi_cd" => $this->input->post("klasifikasi_cd")
            );
            $this->tsession->set_userdata("klasifikasi_search", $params);
        }
        // redirect
        redirect("pengaturan/klasifikasi");
    }

    // add data
    public function add() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/klasifikasi/add.html");
        // list kategori
        $this->smarty->assign("rs_kategori", $this->m_klasifikasi->get_list_kategori());
        // list klasifikasi parent
        $this->smarty->assign("rs_klasifikasi", $this->m_klasifikasi->get_list_all_klasifikasi());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('klas_id', 'Kategori', 'trim|required');
        $this->tnotification->set_rules('klasifikasi_cd', 'Kode Klasifikasi', 'trim|required|max_length[11]');
        $this->tnotification->set_rules('klasifikasi_parent', 'Induk Klasifikasi', 'trim');
        $this->tnotification->set_rules('klasifikasi_name', 'Nama Klasifikasi', 'trim|required|max_length[150]');
        // validate id
        $klasifikasi_cd = $this->m_klasifikasi->get_klasifikasi_id_st($this->input->post('klasifikasi_parent'), $this->input->post('klasifikasi_cd'));
        if (empty($klasifikasi_cd)) {
            $this->tnotification->set_error_message("Kode Klasifikasi Sudah Ada!");
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $klasifikasi_cd,
                $this->input->post('klas_id'),
                $this->input->post('klasifikasi_parent'),
                $this->input->post('klasifikasi_name'),
                $this->com_user['user_id']);
            // insert
            if ($this->m_klasifikasi->insert_klasifikasi($params)) {
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
        redirect("pengaturan/klasifikasi/add");
    }

    // edit data
    public function edit($klasifikasi_cd = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/klasifikasi/edit.html");
        // list kategori
        $this->smarty->assign("rs_kategori", $this->m_klasifikasi->get_list_kategori());
        // list klasifikasi parent
        $this->smarty->assign("rs_klasifikasi", $this->m_klasifikasi->get_list_all_klasifikasi());
        // detail
        $result = $this->m_klasifikasi->get_detail_klasifikasi_by_cd($klasifikasi_cd);
        // code
        if (!empty($result)) {
            $result['klasifikasi_cd_old'] = $result['klasifikasi_cd'];
            $result['klasifikasi_cd'] = str_replace($result['klasifikasi_parent'], '', $result['klasifikasi_cd']);
            $result['klasifikasi_cd'] = trim($result['klasifikasi_cd'], '.');
        }
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
        $this->tnotification->set_rules('klas_id', 'Kategori', 'trim|required');
        $this->tnotification->set_rules('klasifikasi_cd_old', 'Kode Klasifikasi Lama', 'trim|required');
        $this->tnotification->set_rules('klasifikasi_cd', 'Kode Klasifikasi', 'trim|required|max_length[11]');
        $this->tnotification->set_rules('klasifikasi_parent', 'Induk Klasifikasi', 'trim');
        $this->tnotification->set_rules('klasifikasi_name', 'Nama Klasifikasi', 'trim|required|max_length[150]');
        // validate id
        $klasifikasi_cd = $this->m_klasifikasi->get_klasifikasi_id_st($this->input->post('klasifikasi_parent'), $this->input->post('klasifikasi_cd'));
        // process
        if ($this->tnotification->run() !== FALSE) {
            // klasifikasi cd
            if (empty($klasifikasi_cd)) {
                $this->tnotification->set_error_message("Kode Klasifikasi Tidak Diubah!");
                $klasifikasi_cd = $this->input->post('klasifikasi_cd_old');
            }
            // params
            $params = array(
                $klasifikasi_cd,
                $this->input->post('klas_id'),
                $this->input->post('klasifikasi_parent'),
                $this->input->post('klasifikasi_name'),
                $this->com_user['user_id'],
                $this->input->post('klasifikasi_cd_old'));
            // update
            if ($this->m_klasifikasi->update_klasifikasi($params)) {
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
        redirect("pengaturan/klasifikasi/edit/" . $klasifikasi_cd);
    }

    // delete data
    public function delete($klasifikasi_cd = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/klasifikasi/delete.html");
        // detail
        $result = $this->m_klasifikasi->get_detail_klasifikasi_by_cd($klasifikasi_cd);
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
        $this->tnotification->set_rules('klasifikasi_cd', 'Kode Klasifikasi', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array($this->input->post('klasifikasi_cd'));
            // delete
            if ($this->m_klasifikasi->delete_klasifikasi($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/klasifikasi/");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/klasifikasi/delete/" . $klasifikasi_cd);
    }   

}