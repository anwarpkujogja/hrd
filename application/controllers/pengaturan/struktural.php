<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class struktural extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();

        //load model
        $this->load->model('m_peng_struktural');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/struktural/list.html");
        // get search parameter
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        //get list
        $this->smarty->assign("rs_result", $this->m_peng_struktural->get_all_penilaian_struktural($params));
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
        $this->smarty->assign("template_content", "pengaturan/struktural/add.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get list
        $this->smarty->assign("rs_pegawai", $this->m_peng_struktural->get_list_pegawai());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    //add proses
    public function add_process() {
        //cek input
        $this->tnotification->set_rules('FS_KD_PEG', 'Pejabat Struktural', 'trim|required');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('FS_KD_PEG'),
                date('Y'),
                $this->com_user['user_name'],
                date('Y-m-d'),
                date('H:i:s')
            );
            //insert
            if ($this->m_peng_struktural->insert($params)) {

                $penilai = $this->input->post('penilai');
                if (!empty($penilai)) {
                    foreach ($penilai as $value) {
                        $this->m_peng_struktural->insert_penilai(array($this->input->post('FS_KD_PEG'), $value));
                    }
                }

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
        redirect("pengaturan/struktural/");
    }


    //proses hapus
    public function delete_process($FS_KD_TRS="") {
        
        if ($this->m_peng_struktural->delete($FS_KD_TRS)) {
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
            redirect("pengaturan/struktural");
        } else {
                //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        
        //default redirect
        redirect("pengaturan/struktural/delete/");
    }

    public function list_pegawai() {
        $instansi = $this->m_peng_struktural->get_list_pegawai();
        $data[] = array();
        $i = 0;
        foreach ($instansi as $key => $value) {
            $data[$i] = array(
                'label' => $value['FS_NM_PEG'],
                'value' => $value['FS_KD_PEG']
            );
            $i++;
        }
        echo json_encode($data);
    }
}