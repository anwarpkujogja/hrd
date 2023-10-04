<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class format_file extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();

        //load model
        $this->load->model('m_formatfile');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/format_file/list.html");
        // get search parameter
        $search = $this->tsession->userdata('formatfile_search');
        if (!empty($search)) {
            $this->smarty->assign('search', $search);
        }
        // search parameters
        $jenis = empty($search['jenis']) ? '%' : '%' . $search['jenis'] . '%';
        $dir_location = empty($search['dir_location']) ? '%' : '%' . $search['dir_location'] . '%';
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/format_file/index/");
        $config['total_rows'] = $this->m_formatfile->get_total_formatfile(array($jenis . '%', $dir_location . '%'));
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
        //get list
        $params = array($jenis, $dir_location, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_formatfile", $this->m_formatfile->get_all_formatfile_limit($params));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/format_file/add.html");
        // get list
        $this->smarty->assign("rs_formatfile", $this->m_formatfile->get_all_formatfile());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    //add proses
    public function add_process() {
        //cek input
        $this->tnotification->set_rules('dir_location', 'Lokasi Folder', 'trim|required');
        $this->tnotification->set_rules('jenis', 'Jenis', 'trim|required');
        $this->tnotification->set_rules('kode_format', 'Kode Format', 'trim|required');
        $this->tnotification->set_rules('header', 'Header Text', 'trim|required');
        $this->tnotification->set_rules('header_row_index', 'Idx Header Row', 'trim|required|numeric');
        $this->tnotification->set_rules('header_column_index', 'Idx Header Col', 'trim|required|numeric');
        $this->tnotification->set_rules('first_content_row', 'Idx First Content', 'trim|required|numeric');
        $this->tnotification->set_rules('selisih_count_footer', 'Selisih Footer', 'trim|required|numeric');
        $this->tnotification->set_rules('ket', 'Keterangan', 'trim');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('dir_location'),
                $this->input->post('jenis'),
                $this->input->post('kode_format'),
                $this->input->post('header'),
                $this->input->post('header_row_index'),
                $this->input->post('header_column_index'),
                $this->input->post('first_content_row'),
                $this->input->post('selisih_count_footer'),
                $this->input->post('ket'),
                $this->com_user['user_id']
            );
            //insert
            if ($this->m_formatfile->insert($params)) {
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
        redirect("pengaturan/format_file/add");
    }

    public function edit($id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/format_file/edit.html");
        //get detail data
        $this->smarty->assign("result", $this->m_formatfile->get_formatfile_by_id($id));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    public function edit_process() {
        //set rule
        $this->_set_page_rule("U");
        //cek input
        $this->tnotification->set_rules('dir_location', 'Lokasi Folder', 'trim|required');
        $this->tnotification->set_rules('jenis', 'Jenis', 'trim|required');
        $this->tnotification->set_rules('kode_format', 'Kode Format', 'trim|required');
        $this->tnotification->set_rules('header', 'Header Text', 'trim|required');
        $this->tnotification->set_rules('header_row_index', 'Idx Header Row', 'trim|required|numeric');
        $this->tnotification->set_rules('header_column_index', 'Idx Header Col', 'trim|required|numeric');
        $this->tnotification->set_rules('first_content_row', 'Idx First Content', 'trim|required|numeric');
        $this->tnotification->set_rules('selisih_count_footer', 'Selisih Footer', 'trim|required|numeric');
        $this->tnotification->set_rules('ket', 'Keterangan', 'trim');
        $this->tnotification->set_rules('id', 'ID Format File', 'trim|required');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('dir_location'),
                $this->input->post('jenis'),
                $this->input->post('kode_format'),
                $this->input->post('header'),
                $this->input->post('header_row_index'),
                $this->input->post('header_column_index'),
                $this->input->post('first_content_row'),
                $this->input->post('selisih_count_footer'),
                $this->input->post('ket'),
                $this->com_user['user_id'],
                $this->input->post('id')
            );
            //insert
            if ($this->m_formatfile->update($params)) {
                //$this->tnotification->delete_last_field();
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
        redirect("pengaturan/format_file/edit/" . $this->input->post('prov_id', 0));
    }

    public function delete($id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/format_file/delete.html");
        //get detail data
        $this->smarty->assign("result", $this->m_formatfile->get_formatfile_by_id($id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    //proses hapus
    public function delete_process() {
        //set rule
        $this->_set_page_rule("D");
        //cek input
        $this->tnotification->set_rules('id', 'trim|required');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            if ($this->m_formatfile->delete($this->input->post('id'))) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
                redirect("pengaturan/format_file");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        //default redirect
        redirect("pengaturan/format_file/delete/");
    }

    //proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('formatfile_search');
        } else {
            $params = array(
                "jenis" => $this->input->post("jenis"),
                "dir_location" => $this->input->post("dir_location")
            );
            $this->tsession->set_userdata("formatfile_search", $params);
        }
        // redirect
        redirect("pengaturan/format_file");
    }

    public function reset_cari() {
        $this->session->unset_userdata("formatfile_search");
        redirect("pengaturan/format_file");
    }

}