<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class payroll extends ApplicationBase {

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
        $this->smarty->assign("template_content", "data_kepeg/payroll/index.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $year_now = date('Y') + 1;
        for ($i = ($year_now); $i >= ($year_now - 3); $i--) {
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
        $search = $this->tsession->userdata('data_kepeg_payroll_search');
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
        $this->smarty->assign("FS_KD_BAGIAN", $search['FS_KD_BAGIAN']);
        $this->smarty->assign("jam_lembur", $search['jam_lembur']);
        $this->smarty->assign("jam_lembur_l", $search['jam_lembur_l']);
        
       
        $bulan = empty($search['bulan']) ? : $search['bulan'];
        $tahun = empty($search['tahun']) ? : $search['tahun'];
        $FS_KD_BAGIAN = empty($search['FS_KD_BAGIAN']) ? : $search['FS_KD_BAGIAN'];
        $jam_lembur = empty($search['jam_lembur']) ? : $search['jam_lembur'];
        $jam_lembur_l = empty($search['jam_lembur_l']) ? : $search['jam_lembur_l'];
        // pagination
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $this->smarty->assign("rs_unit", $this->m_kepeg_payroll->get_unit());
        $this->smarty->assign("rs_peg", $this->m_kepeg_payroll->get_peg_by_bagian(array($FS_KD_BAGIAN, $bulan, $tahun)));
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
            $this->tsession->unset_userdata("data_kepeg_payroll_search");
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun"),
                "FS_KD_BAGIAN" => $this->input->post("FS_KD_BAGIAN"),
               
            );
            $this->tsession->set_userdata("data_kepeg_payroll_search", $params);
        }
        // redirect
        redirect("data_kepeg/payroll");
    }
    
  

    public function add_payroll_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        // users
        $this->tnotification->set_rules('FS_KD_PEG', 'Kode pegawai', 'trim|required');
        $this->tnotification->set_rules('FN_PERIODE', 'Periode', 'required');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                        $this->input->post('FS_KD_PEG'),
                        $this->input->post('FN_PERIODE'),
                        $this->input->post('FN_GAPOK'),
                        $this->input->post('FN_TUNJ_SUAMI'),
                        $this->input->post('FN_TUNJ_ANAK'),
                        $this->input->post('FN_TUNJ_DAPENMUH'),
                        $this->input->post('FN_TUNJ_BERAS'),
                        $this->input->post('FN_TUNJ_JABATAN'),
                        $this->input->post('FN_TUNJ_BPJS'),
                        $this->input->post('FN_TUNJ_BPJS_TK'),

                        $this->input->post('FN_TUNJ_FUNGSIONAL'),
                        $this->input->post('FN_TUNJ_PROFESI'),
                        $this->input->post('FN_TUNJ_THD'),
                        $this->input->post('FN_TUNJ_LEMBUR'),
                        $this->input->post('FN_TUNJ_PENDIDIKAN'),
                        $this->input->post('FN_TUNJ_MAKAN'),
                        $this->input->post('FN_TUNJ_TKP'),

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
                        $this->com_user['user_name'],

                        '2023-01-01',
                        date('H:i:s'),
                        $this->input->post('FS_KD_BAGIAN'),
                        $this->input->post('FN_TUNJ_IPK'),
                        $this->input->post('FN_TUNJ_CUTI'),
                        $this->input->post('FN_TUNJ_RAPEL'),
                        $this->input->post('FN_TUNJ_IHR'),
                        $this->input->post('MaKer'),
                        $this->input->post('FN_TUNJ_PULSA'),
                        $this->input->post('FN_TUNJ_ONCALL'),

                        $this->input->post('FN_TUNJ_LEMBUR_L'),
                        $this->input->post('FN_TUNJ_THD_MAN'),
                        $this->input->post('FN_KET_RAPEL'),
                        $this->input->post('FN_KET_POT'),
                        $this->input->post('FN_PTKP'),
                        $this->input->post('FN_TUNJ_OVERTIME')
                        

            );
            //insert
           //var_dump($params);
           if ($this->m_kepeg_payroll->insert_payroll($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                redirect("data_kepeg/payroll/add_payroll");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        } 
        //default redirect
        redirect("data_kepeg/payroll/add_payroll");
    } 

    public function edit($FS_KD_PAYROLL ="", $FS_KD_PEG="") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/payroll/edit.html");
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
           
            $this->tnotification->set_rules('FS_KD_PEG', 'Pegawai', 'trim|required');
            //proses
            if ($this->tnotification->run() !== FALSE) {
                $params = array(
                    $this->input->post('FS_KD_PEG'),
                    $this->input->post('FN_PERIODE'),
                    $this->input->post('FN_GAPOK'),
                    $this->input->post('FN_TUNJ_SUAMI'),
                    $this->input->post('FN_TUNJ_ANAK'),
                    $this->input->post('FN_TUNJ_DAPENMUH'),
                    $this->input->post('FN_TUNJ_BERAS'),
                    $this->input->post('FN_TUNJ_JABATAN'),
                    $this->input->post('FN_TUNJ_BPJS'),
                    $this->input->post('FN_TUNJ_BPJS_TK'),
                    $this->input->post('FN_TUNJ_FUNGSIONAL'),
                    $this->input->post('FN_TUNJ_PROFESI'),
                    $this->input->post('FN_TUNJ_THD'),
                    $this->input->post('FN_TUNJ_LEMBUR'),
                    $this->input->post('FN_TUNJ_PENDIDIKAN'),
                    $this->input->post('FN_TUNJ_MAKAN'),
                    $this->input->post('FN_TUNJ_TKP'),

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
                    $this->com_user['user_name'],
                    '2023-01-01',
                    date('H:i:s'),
                    $this->input->post('FS_KD_BAGIAN'),
                    $this->input->post('FN_TUNJ_IPK'),
                    $this->input->post('FN_TUNJ_CUTI'),
                    $this->input->post('FN_TUNJ_RAPEL'),
                    $this->input->post('FN_TUNJ_IHR'),
                   
                    $this->input->post('FN_TUNJ_PULSA'),
                    $this->input->post('FN_TUNJ_ONCALL'),
                    $this->input->post('FN_TUNJ_LEMBUR_L'),
                    $this->input->post('FN_TUNJ_THD_MAN'),
                    $this->input->post('FN_KET_RAPEL'),
                    $this->input->post('FN_KET_POT'),
                    $this->input->post('FN_TUNJ_RAPELKE'),
                    $this->input->post('FS_RAPELKE_KET'),
                    $this->input->post('FN_BSM'),
                    $this->input->post('FN_TUNJ_OVERTIME'),
                    $this->input->post('FN_PTKP'),
                    $this->input->post('FS_KD_PAYROLL')
                       
                );
                //var_dump($params);
                //insert
                if ($this->m_kepeg_payroll->update_payroll($params)) {
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
            redirect("data_kepeg/payroll/edit/".$this->input->post('FS_KD_PAYROLL').'/'.$this->input->post('FS_KD_PEG'));
    }

    public function slip($FS_KD_PAYROLL = "", $FS_KD_PEG = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        //$search = $this->tsession->userdata('ipk_search');
        // search parameters
        //$periode = empty($search['periode']) ? '0' : $search['periode'];
        //$tahun = empty($search['tahun']) ? $year : $search['tahun'];
        $data['res'] = $this->m_kepeg_payroll->get_data_peg(array(date('Y-m-d'),$FS_KD_PEG));

        $data['results'] = $this->m_kepeg_payroll->getdata_payroll_perpegawai(array($FS_KD_PEG . '-A%', $FS_KD_PAYROLL));

     
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

    public function slip_bonus($FS_KD_PAYROLL = "", $FS_KD_PEG = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        //$search = $this->tsession->userdata('ipk_search');
        // search parameters
        //$periode = empty($search['periode']) ? '0' : $search['periode'];
        //$tahun = empty($search['tahun']) ? $year : $search['tahun'];
        $data['res'] = $this->m_kepeg_payroll->get_data_peg(array(date('Y-m-d'),$FS_KD_PEG));

        $data['results'] = $this->m_kepeg_payroll->getdata_payroll_perpegawai(array($FS_KD_PEG . '-A%', $FS_KD_PAYROLL));

     
        ob_start();
        $this->load->view('data_kepeg/payroll/slip_bonus', $data);
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

    public function add_payroll($FS_KD_PEG = "")
    {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->assign("template_content", "data_kepeg/payroll/detail.html");
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


        //assign periode

        $this->smarty->assign("tanggal", $search['tanggal']);
        $this->smarty->assign("tanggal2", $search['tanggal2']);
        $this->smarty->assign("periode_lembur", $search['periode_lembur']);

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
        $tanggal = empty($search['tanggal']) ? date('Y-m-d') : $search['tanggal'];
        $tanggal2 = empty($search['tanggal2']) ? date('Y-m-d') : $search['tanggal2'];
        $periode_lembur = empty($search['periode_lembur']) ? '' : $search['periode_lembur'];
        $periode_overtime = empty($search['periode_overtime']) ? '' : $search['periode_overtime'];
        $params = array(date('Y-m-d'), $FS_KD_PEG . '-A%' ,  $FS_KD_PEG . '-S%', $FS_KD_PEG . '-I%', $FS_KD_PEG);;
        //var_dump($params);
        $params2 = array($tanggal, $tanggal2,
                         $tanggal, $tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal2,
                         $tanggal,$tanggal,
                         $tanggal2,$FS_KD_PEG);
        $params3 = array($periode_lembur, $FS_KD_PEG);
        $params4 = array($periode_overtime, $FS_KD_PEG);
        $this->smarty->assign("result", $this->m_kepeg_payroll->get_pegawai($params));
        $this->smarty->assign("results", $this->m_kepeg_payroll->get_THD($params2));
        $this->smarty->assign("lembur", $this->m_kepeg_payroll->get_lembur_peg($params3));  
        $this->smarty->assign("overtime", $this->m_kepeg_payroll->get_overtime_peg($params4));  
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
                "FS_KD_PEG" => $this->input->post("FS_KD_PEG"),
                "tanggal" => $this->input->post("tanggal"),
                "tanggal2" => $this->input->post("tanggal2"),
                "jam_lembur" => $this->input->post("jam_lembur"),
                "jam_lembur_l" => $this->input->post("jam_lembur_l"),
                'is_cuti'   => $this->input->post("is_cuti"),
                'periode_lembur' => $this->input->post("periode_lembur"),
                'periode_overtime' => $this->input->post("periode_overtime")
            );
            $this->tsession->set_userdata("data_kepeg_payroll_search", $params);
        }
        // redirect
        redirect("data_kepeg/payroll/add_payroll");
    }

    public function proses_cari_thd() {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("data_kepeg_thd_search");
        } else {
            $params = array(
                "FS_KD_PEG" => $this->input->post("FS_KD_PEG"),
                "tanggal" => $this->input->post("tanggal"),
                "tanggal2" => $this->input->post("tanggal2")
                
            );
            $this->tsession->set_userdata("data_kepeg_thd_search", $params);
        }
        // redirect
        redirect("data_kepeg/payroll/add_payroll");
    }

    public function delete_process($FS_KD_PAYROLL="") {
        // set page rules
        $this->_set_page_rule("D");
        // process
            $params = array($FS_KD_PAYROLL);
            // insert
            if ($this->m_kepeg_payroll->delete_payroll($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // redirect to list
                redirect("data_kepeg/payroll/");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        
        // default redirect
        redirect("data_kepeg/payroll/");
    }


    public function report_excel_unit(){
        // Load plugin PHPExcel nya
        $this->load->library('PHPexcel');
    
      
        $search = $this->tsession->userdata('data_kepeg_payroll_search');
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
        $this->smarty->assign("is_cuti", $search['is_cuti']);
        $this->smarty->assign("periode_lembur", $search['periode_lembur']);
        $this->smarty->assign("periode_overtime", $search['periode_overtime']);
    
        $bulan = empty($search['bulan']) ? : $search['bulan'];
        $tahun = empty($search['tahun']) ? : $search['tahun'];
        $FS_KD_BAGIAN = empty($search['FS_KD_BAGIAN']) ? : $search['FS_KD_BAGIAN'];

        // pagination
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        
        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        // Settingan awal fil excel
        $excel->getProperties()->setCreator('RS PKU MUhamadiyah Gamping')
                     ->setLastModifiedBy('RS PKU MUhamadiyah Gamping')
                     ->setTitle("Data Rekap Gaji Per Unit")
                     ->setSubject("Data Rekap Gaji Per Unit")
                     ->setDescription("Data Rekap Gaji Per Unit")
                     ->setKeywords("Data Rekap Gaji Per Unit");
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
          'font' => array('bold' => true), // Set font nya jadi bold
          'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ),
          'borders' => array(
            'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
            'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
            'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
            'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
          )
        );
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
          'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ),
          'borders' => array(
            'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
            'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
            'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
            'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
          )
        );
       
        $tanggal = $this->m_kepeg_payroll->get_periode_bulan_bagian(array($FS_KD_BAGIAN, $bulan, $tahun)); 
        $excel->setActiveSheetIndex(0)->setCellValue('A1', "REKAP GAJI PEGAWAI UNIT " . $tanggal['FS_NM_BAGIAN']); 
        $excel->setActiveSheetIndex(0)->setCellValue('A2', "RS PKU MUHAMMADIYAH GAMPING");
        if($tanggal['FN_PERIODE'] == 1){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'JANUARI' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 2){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'FEBRUARI' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 3){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'MARET' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 4){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'APRIL' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 5){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'MEI' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 6){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'JUNI' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 7){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'JULI' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 8){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'AGUSTUS' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 9){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'SEPTEMBER' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 10){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'OKTOBER' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 11){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'NOVEMBER' ." 2023"); 
        }
        if($tanggal['FN_PERIODE'] == 12){
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "BULAN " . 'DESEMBER' ." 2023"); 
        }
   
        $excel->getActiveSheet()->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); // Set text center untuk kolom A1
        $excel->getActiveSheet()->mergeCells('A2:E2'); // Set Merge Cell pada kolom A1 sampai E1
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); // Set text center untuk kolom A1
        $excel->getActiveSheet()->mergeCells('A3:E3'); // Set Merge Cell pada kolom A1 sampai E1
        $excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        $excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); // Set text center untuk kolom A1
        // Buat header tabel nya pada baris ke 3
        $excel->setActiveSheetIndex(0)->setCellValue('A5', "NO"); // Set kolom A5 dengan tulisan "NO"
        $excel->setActiveSheetIndex(0)->setCellValue('B5', "NIK"); // Set kolom B5 dengan tulisan "NIS"
        $excel->setActiveSheetIndex(0)->setCellValue('C5', "NAMA"); // Set kolom B5 dengan tulisan "NIS"
        $excel->setActiveSheetIndex(0)->setCellValue('D5', "GAPOK"); 
        $excel->setActiveSheetIndex(0)->setCellValue('E5', "TJ SUAMI"); 
        $excel->setActiveSheetIndex(0)->setCellValue('F5', "TJ ANAK"); 
        $excel->setActiveSheetIndex(0)->setCellValue('G5', "TJ DAPENMUH"); 
        $excel->setActiveSheetIndex(0)->setCellValue('H5', "TJ BERAS"); 
        $excel->setActiveSheetIndex(0)->setCellValue('I5', "TJ JABATAN"); 
        $excel->setActiveSheetIndex(0)->setCellValue('J5', "TJ BPJS"); 
        $excel->setActiveSheetIndex(0)->setCellValue('K5', "TJ BPJS_TK"); 
        $excel->setActiveSheetIndex(0)->setCellValue('L5', "TJ FUNG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('M5', "TJ PROF"); 
        $excel->setActiveSheetIndex(0)->setCellValue('N5', "TJ THD"); 
        $excel->setActiveSheetIndex(0)->setCellValue('O5', "TJ LEMB"); 
        $excel->setActiveSheetIndex(0)->setCellValue('P5', "TJ PEND"); 
        $excel->setActiveSheetIndex(0)->setCellValue('Q5', "TJ MAKAN"); 
        $excel->setActiveSheetIndex(0)->setCellValue('R5', "TJ TKP"); 
        $excel->setActiveSheetIndex(0)->setCellValue('S5', "TJ IPK"); 
        $excel->setActiveSheetIndex(0)->setCellValue('T5', "TJ CUTI"); 
        $excel->setActiveSheetIndex(0)->setCellValue('U5', "TJ RAPEL"); 
        $excel->setActiveSheetIndex(0)->setCellValue('V5', "TJ HR RAYA"); 
        $excel->setActiveSheetIndex(0)->setCellValue('W5', "TJ PULSA"); 
        $excel->setActiveSheetIndex(0)->setCellValue('X5', "PPH21"); 
        $excel->setActiveSheetIndex(0)->setCellValue('Y5', "POT BPJS"); 
        $excel->setActiveSheetIndex(0)->setCellValue('Z5', "POT BPJSTK"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AA5', "POT BRI"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AB5', "POT BPD"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AC5', "POT BAROKAH"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AD5', "POT FARMASI"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AE5', "POT ALIKHLAS"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AF5', "POT PERUM"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AG5', "POT INFAQPP"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AH5', "POT DAPENMUH"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AI5', "POT LAIN"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AJ5', "POT BSM"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AK5', "POT BPJSTK RS"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AL5', "POT DAPENMUH RS"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AM5', "TOTAL BRUTO"); 
        $excel->setActiveSheetIndex(0)->setCellValue('AN5', "TERIMA BERSIH"); 
     

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $excel->getActiveSheet()->getStyle('A5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('G5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('H5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('I5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('J5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('K5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('L5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('M5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('N5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('O5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('P5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('Q5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('R5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('S5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('T5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('U5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('V5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('W5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('X5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('Y5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('Z5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AA5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AB5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AC5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AD5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AE5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AF5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AG5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AH5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AI5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AJ5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AK5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AL5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AM5')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('AN5')->applyFromArray($style_col);

        // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
        $karya = $this->m_kepeg_payroll->get_rekapexcel_unit(array($FS_KD_BAGIAN, $bulan, $tahun));
        $no = 1; // Untuk penomoran tabel, di awal set dengan 1
        $numrow = 6; // Set baris pertama untuk isi tabel adalah baris ke 4
        foreach($karya as $data){ // Lakukan looping pada variabel siswa
            $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $data->FS_KD_PEG);
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $data->FS_NM_PEG);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $data->FN_GAPOK);
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $data->FN_TUNJ_SUAMI);
            $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $data->FN_TUNJ_ANAK);
            $excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $data->FN_TUNJ_DAPENMUH);
            $excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $data->FN_TUNJ_BERAS);
            $excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $data->FN_TUNJ_JABATAN);
            $excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $data->FN_TUNJ_BPJS);
            $excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $data->FN_TUNJ_BPJS_TK);
            $excel->setActiveSheetIndex(0)->setCellValue('L'.$numrow, $data->FN_TUNJ_FUNGSIONAL);
            $excel->setActiveSheetIndex(0)->setCellValue('M'.$numrow, $data->FN_TUNJ_PROFESI);
            $excel->setActiveSheetIndex(0)->setCellValue('N'.$numrow, $data->FN_TUNJ_THD);
            $excel->setActiveSheetIndex(0)->setCellValue('O'.$numrow, $data->FN_TUNJ_LEMBUR +  $data->FN_TUNJ_LEMBUR_L);
            $excel->setActiveSheetIndex(0)->setCellValue('P'.$numrow, $data->FN_TUNJ_PENDIDIKAN);
            $excel->setActiveSheetIndex(0)->setCellValue('Q'.$numrow, $data->FN_TUNJ_MAKAN);
            $excel->setActiveSheetIndex(0)->setCellValue('R'.$numrow, $data->FN_TUNJ_TKP);
            $excel->setActiveSheetIndex(0)->setCellValue('S'.$numrow, $data->FN_TUNJ_IPK);
            $excel->setActiveSheetIndex(0)->setCellValue('T'.$numrow, $data->FN_TUNJ_CUTI);
            $excel->setActiveSheetIndex(0)->setCellValue('U'.$numrow, $data->FN_TUNJ_RAPEL);
            $excel->setActiveSheetIndex(0)->setCellValue('V'.$numrow, $data->FN_TUNJ_IHR);
            $excel->setActiveSheetIndex(0)->setCellValue('W'.$numrow, $data->FN_TUNJ_PULSA);
            $excel->setActiveSheetIndex(0)->setCellValue('X'.$numrow, $data->FN_PPH_21);
            $excel->setActiveSheetIndex(0)->setCellValue('Y'.$numrow, $data->FN_BPJS);
            $excel->setActiveSheetIndex(0)->setCellValue('Z'.$numrow, $data->FN_BPJS_TK);
            $excel->setActiveSheetIndex(0)->setCellValue('AA'.$numrow, $data->FN_BRI);
            $excel->setActiveSheetIndex(0)->setCellValue('AB'.$numrow, $data->FN_BPD);
            $excel->setActiveSheetIndex(0)->setCellValue('AC'.$numrow, $data->FN_BAROKAH);
            $excel->setActiveSheetIndex(0)->setCellValue('AD'.$numrow, $data->FN_FARMASI);
            $excel->setActiveSheetIndex(0)->setCellValue('AE'.$numrow, $data->FN_AL_IKHLAS);
            $excel->setActiveSheetIndex(0)->setCellValue('AF'.$numrow, $data->FN_PERUMAHAN);
            $excel->setActiveSheetIndex(0)->setCellValue('AG'.$numrow, $data->FN_INFAQ_PP);
            $excel->setActiveSheetIndex(0)->setCellValue('AH'.$numrow, $data->FN_DAPENMUH);
            $excel->setActiveSheetIndex(0)->setCellValue('AI'.$numrow, $data->FN_LAIN_LAIN);
            $excel->setActiveSheetIndex(0)->setCellValue('AJ'.$numrow, $data->FN_BSM);
            $excel->setActiveSheetIndex(0)->setCellValue('AK'.$numrow, $data->FN_TUNJ_BPJS_TK);
            $excel->setActiveSheetIndex(0)->setCellValue('AL'.$numrow, $data->FN_TUNJ_DAPENMUH);

            $excel->setActiveSheetIndex(0)->setCellValue('AM'.$numrow, $data->JmlBruto);
            $excel->setActiveSheetIndex(0)->setCellValue('AN'.$numrow, $data->TERIMABERSIH);
          
          /*$excel->setActiveSheetIndex(0)->setCellValue('AI'.$numrow, $data->FN_PERUMAHAN);
          $excel->setActiveSheetIndex(0)->setCellValue('AJ'.$numrow, $data->FN_PERUMAHAN);
          $excel->setActiveSheetIndex(0)->setCellValue('AK'.$numrow, $data->FN_PERUMAHAN);
          $excel->setActiveSheetIndex(0)->setCellValue('AL'.$numrow, $data->FN_PERUMAHAN);
          $excel->setActiveSheetIndex(0)->setCellValue('AM'.$numrow, $data->FN_PERUMAHAN);
          $excel->setActiveSheetIndex(0)->setCellValue('AN'.$numrow, $data->FN_PERUMAHAN);
          $excel->setActiveSheetIndex(0)->setCellValue('AO'.$numrow, $data->FN_PERUMAHAN);*/
          
          // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
          $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('G'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('H'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('I'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('J'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('K'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('L'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('M'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('N'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('O'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('P'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('Q'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('R'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('S'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('T'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('U'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('V'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('W'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('X'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('Y'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('Z'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AA'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AB'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AC'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AD'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AE'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AF'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AG'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AH'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AI'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AJ'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AK'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AL'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AM'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AN'.$numrow)->applyFromArray($style_row);
          /*$excel->getActiveSheet()->getStyle('AJ'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AK'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AL'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AM'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AN'.$numrow)->applyFromArray($style_row);
          $excel->getActiveSheet()->getStyle('AO'.$numrow)->applyFromArray($style_row);*/
          $no++; // Tambah 1 setiap kali looping
          $numrow++; // Tambah 1 setiap kali looping
        }
        // Set width kolom
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); // Set width kolom A
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(10); // Set width kolom B
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(28); // Set width kolom C
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(12); 
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(12); 
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(12); 
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(12); 
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(12); 
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('R')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('S')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('T')->setWidth(15);  
        $excel->getActiveSheet()->getColumnDimension('U')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('V')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('W')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('X')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('Y')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('Z')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AA')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AB')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AC')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AD')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AE')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AF')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AG')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AH')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AI')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AJ')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AK')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AL')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AM')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('AN')->setWidth(15); 

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $excel->getActiveSheet(0)->setTitle("REKAP GAJI PEGAWAI UNIT");
        $excel->setActiveSheetIndex(0);
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="GAJI_PEGAWAI_UNIT.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');    
      }


      
        

    
   
    
}
