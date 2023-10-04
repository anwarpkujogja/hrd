<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class gaji_berkala extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_kepeg_payroll');
        $this->smarty->assign('m_kepeg_payroll', $this->m_kepeg_payroll);
        $this->load->model('m_kepeg_gaji_berkala');
        $this->smarty->assign('m_kepeg_gaji_berkala', $this->m_kepeg_gaji_berkala);
        
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list surat masuk
    public function index($FS_KD_PEG = "") {
       // set page rules
       $this->_set_page_rule("C");
       // set template content
       $this->smarty->assign("template_content", "data_kepeg/gaji_berkala/index.html");
       $search = $this->tsession->userdata('data_kepeg_payroll_search');
       if (!empty($search)) {
           $this->smarty->assign("search", $search);
       } else {
           $this->smarty->assign("search", $search);
       }
       $year_now = date('Y');
       for ($i = ($year_now); $i >= ($year_now - 0); $i--) {
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
       $this->smarty->assign("rs_tahun", $tahun);

       $FS_KD_PEG = empty($search['FS_KD_PEG']) ? '' : $search['FS_KD_PEG'];
       $params = array($FS_KD_PEG);
       $this->smarty->assign("result", $this->m_kepeg_gaji_berkala->get_pegawai(array(date('Y-m-d'),$FS_KD_PEG)));
       $this->smarty->assign("rs_pangkat", $this->m_kepeg_gaji_berkala->get_pangkat());
       $this->smarty->assign("rs_status", $this->m_kepeg_gaji_berkala->get_status());
       $this->smarty->assign("rs_golongan", $this->m_kepeg_gaji_berkala->get_golongan());
       $this->smarty->assign("rs_aktif", $this->m_kepeg_gaji_berkala->get_aktif());
       $this->smarty->assign("rs_jenis", $this->m_kepeg_gaji_berkala->get_jenis_kenaikan_gaji());
       $this->smarty->assign("history", $this->m_kepeg_gaji_berkala->get_history_peg(array($FS_KD_PEG)));
       
       //$this->smarty->assign("result", $this->m_ipk->get_data_peg_by_search($params));
       // output
       $this->tnotification->display_notification();
       $this->tnotification->display_last_field();
       parent::display();
    } 

   public function proses_cari_detil() {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("data_kepeg_payroll_search");
        } else {
            $params = array(
                "FS_KD_PEG" => $this->input->post("FS_KD_PEG")
            );
            $this->tsession->set_userdata("data_kepeg_payroll_search", $params);
        }
        // redirect
        redirect("data_kepeg/gaji_berkala/index");
    }
    
    /*public function add($FS_KD_PEG = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/payroll/add.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");

        $this->smarty->assign("result", $this->m_kepeg_payroll->get_pegawai(array($FS_KD_PEG)));
        $this->smarty->assign("rs_pendapatan", $this->m_kepeg_payroll->get_list_pendapatan());
        $this->smarty->assign("rs_pengurangan", $this->m_kepeg_payroll->get_list_pengurangan());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }*/

    public function add_gajiberkala_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $FD_KD_TRS = $this->m_kepeg_gaji_berkala->get_no_trs_kenaikan();
        $FS_KD_TRS_KENAIKAN = $FD_KD_TRS['KENAIKAN'] + 1; 
        $FS_KD_TRS_FIN = $FS_KD_TRS_KENAIKAN;
        // users
        $this->tnotification->set_rules('FS_KD_PEG', 'Kode pegawai', 'trim|required');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('FS_KD_PEG'),
                $this->input->post('FS_NO_SK'),
                $this->input->post('FD_TGL_SK'),
                $this->input->post('FS_NM_PEJABAT'),
                $this->input->post('FN_GAJI_POKOK'),
                $this->input->post('FB_AKTIF'),
                $this->input->post('FN_TUNJANGAN1'),
                $this->input->post('FN_TUNJANGAN2'),
                $this->input->post('FN_TUNJANGAN3'),
                $this->input->post('FN_TUNJANGAN4'),
                $this->input->post('FN_TUNJANGAN5'),
                $this->input->post('FN_TUNJANGAN6'),
                $this->input->post('FN_TUNJANGAN7'),
                $this->input->post('FN_TUNJANGAN8'),
                //$this->input->post('FS_KD_PANGKAT'),
                $FS_KD_TRS_FIN,
                $this->input->post('FS_KD_JENIS_KENAIKAN_GAJI'),
                $this->input->post('FS_KD_GOLONGAN'),
                $this->input->post('FN_TUNJANGAN9'),     
                $this->input->post('FN_TJ_PULSA')       
            );
            //insert
            //var_dump($params);
            if ($this->m_kepeg_gaji_berkala->insert_gaji_berkala($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                redirect("data_kepeg/gaji_berkala");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        //default redirect
        redirect("data_kepeg/gaji_berkala");
    }

    public function edit($FS_KD_PEG ="", $FS_KD_TRS ="") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/gaji_berkala/edit.html");
        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        //$arr = array($FS_KD_PEG, $FS_KD_PEG);
        $this->smarty->assign("rs_tahun", $tahun);
        $this->smarty->assign("result", $this->m_kepeg_gaji_berkala->get_edit_pegawai(array($FS_KD_PEG, $FS_KD_TRS)));
        $this->smarty->assign("rs_pangkat", $this->m_kepeg_gaji_berkala->get_pangkat());
        $this->smarty->assign("rs_status", $this->m_kepeg_gaji_berkala->get_status());
        $this->smarty->assign("rs_golongan", $this->m_kepeg_gaji_berkala->get_golongan());
        $this->smarty->assign("rs_aktif", $this->m_kepeg_gaji_berkala->get_aktif());
        $this->smarty->assign("rs_jenis", $this->m_kepeg_gaji_berkala->get_jenis_kenaikan_gaji());
        //$this->smarty->assign("results", $this->m_kepeg_payroll->get_payroll_perpegawai(array($FS_KD_PEG)));
        
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        parent::display();
    }   
    
    public function update_gajiberkala_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        // users
        $this->tnotification->set_rules('FN_GAJI_POKOK', 'Gaji Pokok', 'required');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                
                $this->input->post('FS_NO_SK'),
                $this->input->post('FD_TGL_SK'),
                $this->input->post('FS_NM_PEJABAT'),
                $this->input->post('FN_GAJI_POKOK'),
                $this->input->post('FB_AKTIF'),
                $this->input->post('FN_TUNJANGAN1'),
                $this->input->post('FN_TUNJANGAN2'),
                $this->input->post('FN_TUNJANGAN3'),
                $this->input->post('FN_TUNJANGAN4'),
                $this->input->post('FN_TUNJANGAN5'),
                $this->input->post('FN_TUNJANGAN6'),
                $this->input->post('FN_TUNJANGAN7'),
                $this->input->post('FN_TUNJANGAN8'),
                //$this->input->post('FS_KD_PANGKAT'),
                $this->input->post('FS_KD_JENIS_KENAIKAN_GAJI'),
                $this->input->post('FS_KD_GOLONGAN'),
                $this->input->post('FN_TUNJANGAN9'),   
                $this->input->post('FN_TJ_PULSA'),   
                $this->input->post('FS_KD_TRS_KENAIKAN')
            );
            //insert
            var_dump($params);
            if ($this->m_kepeg_gaji_berkala->update_gaji_berkala($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                
            
                redirect("data_kepeg/gaji_berkala");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        //default redirect
        redirect("data_kepeg/gaji_berkala");
    }

    public function slip($FS_KD_PEG = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        //$search = $this->tsession->userdata('ipk_search');
        // search parameters
        //$periode = empty($search['periode']) ? '0' : $search['periode'];
        //$tahun = empty($search['tahun']) ? $year : $search['tahun'];
        
        $data['results'] = $this->m_kepeg_payroll->getdata_payroll_perpegawai($FS_KD_PEG);
        
        ob_start();
        $this->load->view('data_kepeg/payroll/slip', $data);
        $content = ob_get_contents();
        ob_end_clean();

        try {
            $html2pdf = new HTML2PDF();
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            $html2pdf->Output($FS_KD_PEG . '.pdf');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }

    

    


    
}
