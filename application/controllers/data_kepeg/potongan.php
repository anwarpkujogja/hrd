<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class potongan extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_kepeg_payroll');
        $this->smarty->assign('m_kepeg_payroll', $this->m_kepeg_payroll);
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }


    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/potongan/index.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $year_now = date('Y',strtotime('+1 year'));
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        $bulan = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );

        $this->smarty->assign("rs_bulan", $bulan);
        // default search
        $year = date("Y");
        $month = date("m");
        // get search parameter
        $search = $this->tsession->userdata('data_kepeg_potongan_search');
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        } else {
            $search['bulan'] = $month;
            $search['tahun'] = $year;
            $this->smarty->assign("search", $search);
        }
        
        // assign periode

        $this->smarty->assign("tahun", $search['tahun']);
        $this->smarty->assign("bulan", $search['bulan']);
        $this->smarty->assign("FS_KD_PEG", $search['FS_KD_PEG']);
        
       
        $bulan = empty($search['bulan']) ? : $search['bulan'];
        $tahun = empty($search['tahun']) ? : $search['tahun'];
        $FS_KD_BAGIAN = empty($search['FS_KD_BAGIAN']) ? : $search['FS_KD_BAGIAN'];
      
        // pagination
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $this->smarty->assign("rs_unit", $this->m_kepeg_payroll->get_unit());
        $this->smarty->assign("rs_peg", $this->m_kepeg_payroll->get_peg_by_bagian(array($FS_KD_BAGIAN, $bulan, $tahun)));
        // notification
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    } 

   public function proses_cari() {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("data_kepeg_potongan_search");
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun"),
                "FS_KD_BAGIAN" => $this->input->post("FS_KD_BAGIAN"),
               
            );
            $this->tsession->set_userdata("data_kepeg_potongan_search", $params);
        }
        // redirect
        redirect("data_kepeg/potongan");
    }
    

    public function edit($FS_KD_PAYROLL ="", $FS_KD_PEG="") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/potongan/edit.html");
        //set periode bulan
        $bulan = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );

        $this->smarty->assign("rs_bulan", $bulan);

        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
       
        $arr = array($FS_KD_PEG, $FS_KD_PEG, $FS_KD_PEG, $FS_KD_PEG, $FS_KD_PEG);
        $this->smarty->assign("rs_tahun", $tahun);
        $this->smarty->assign("result", $this->m_kepeg_payroll->get_pegawai($arr));
        $this->smarty->assign("results", $this->m_kepeg_payroll->get_payroll_perpegawai(array($FS_KD_PAYROLL, $FS_KD_PEG)));
     
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        parent::display();
    }   
    
    public function edit_payroll_process() {
        // set page rules
        $this->_set_page_rule("U");
            // users
           
            $this->tnotification->set_rules('FS_KD_PAYROLL', 'Pegawai', 'trim|required');
            //proses
            if ($this->tnotification->run() !== FALSE) {
                $params = array(
        
                    $this->input->post('FN_PPH_21'),
                    $this->input->post('FN_BPJS'),
                    $this->input->post('FN_BPJS_TK'),
                    $this->input->post('FN_BRI'),
                    $this->input->post('FN_BPD'),
                    $this->input->post('FN_BAROKAH'),
                    $this->input->post('FN_FARMASI'),
                    $this->input->post('FN_AL_IKHLAS'),
                    $this->input->post('FN_PERUMAHAN'),
                    $this->input->post('FN_INFAQ_PP'),
                    $this->input->post('FN_DAPENMUH'),
                    $this->input->post('FN_LAIN_LAIN'),
                    $this->input->post('FN_KET_POT'),
                    $this->input->post('FN_BSM'),
                    $this->input->post('FS_KD_PAYROLL')
                       
                );
                //var_dump($params);
                //insert
                if ($this->m_kepeg_payroll->potong_payroll($params)) {
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                } else {
                //    //default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                }
            } else {
                //default error
               $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
            //default redirect
            redirect("data_kepeg/potongan/edit/".$this->input->post('FS_KD_PAYROLL').'/'.$this->input->post('FS_KD_PEG'));
    }

}
