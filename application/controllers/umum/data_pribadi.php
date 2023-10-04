<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once(APPPATH . 'controllers/base/OperatorBase.php');

// --

class data_pribadi extends ApplicationBase
{

    // constructor
    public function __construct()
    {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_data_pribadi');
        $this->load->model('m_kepeg_cuti');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list surat masuk
    public function index()
    {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "umum/data_pribadi/index.html");
        // get search parameter
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->assign("tahun", date('Y'));
        $this->smarty->assign("result", $this->m_data_pribadi->get_data_peg(array(date('Y-m-d'), $this->com_user['user_name'])));
        $this->smarty->assign("rs_asuransi_bpjs_peg", $this->m_data_pribadi->get_data_peg_asuransi_bpjs(array($this->com_user['user_name'])));
        $this->smarty->assign("rs_asuransi_jamsos_peg", $this->m_data_pribadi->get_data_peg_asuransi_jamsos(array($this->com_user['user_name'])));
        $this->smarty->assign("rs_cuti_ambil", $this->m_kepeg_cuti->get_cuti_diambil_by_peg(array($this->com_user['user_name'], date('Y-01-01'), date('Y-12-31'))));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit data
    public function edit_process()
    {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('FS_KD_PEG', 'NIK Pegawai', 'trim|required');
        $this->tnotification->set_rules('FS_NM_ALIAS', 'Nama Pegawai', 'trim|required');
        $this->tnotification->set_rules('FS_NM_PEG', 'Nama Lengkap Pegawai', 'trim|required');
        $this->tnotification->set_rules('FB_JNS_KELAMIN', 'Jenis Kelamin Pegawai', 'trim|required');
        $this->tnotification->set_rules('FS_TEMP_LAHIR', 'Tempat Lahir Pegawai', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('FS_NM_ALIAS'),
                $this->input->post('FS_NM_PEG'),
                $this->input->post('FS_KD_NIP'),
                $this->input->post('FB_JNS_KELAMIN'),
                $this->input->post('FS_TEMP_LAHIR'),
                $this->input->post('FD_TGL_LAHIR'),
                $this->input->post('FS_ALM_PEG'),
                $this->input->post('FS_ALM_TINGGAL_PEG'),
                $this->input->post('FS_HP_PEG'),
                $this->input->post('FS_TLP_PEG'),
                $this->input->post('FS_EMAIL'),
                $this->input->post('FS_KD_PEG')
            );
            if ($this->m_data_pribadi->update_pegawai($params)) {
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Detail gagal disimpan");
        }
        // default redirect
        redirect("umum/data_pribadi/");
    }
}
