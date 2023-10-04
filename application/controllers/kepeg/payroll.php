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
        $this->load->model('m_kepeg_payroll_user');
        $this->load->model('m_kepeg_payroll');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "kepeg/payroll/list.html");
        // tahun
        $year_now = date('Y',strtotime('+1 year'));
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // default search
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
         $search = $this->tsession->userdata('payroll_search');
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        } else {
            $search['bulan'] = $month;
            $search['tahun'] = $year;
            $this->smarty->assign("search", $search);
        }
        // assign periode
        $this->smarty->assign("tahun", $search['tahun']);
        $this->smarty->assign("bulan", $bulan[$search['bulan']]);
        // search parameters
        $bulan = empty($search['bulan']) ? $month : $search['bulan'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];

        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $params = array($this->com_user['user_name'],$bulan, $tahun);
        $this->smarty->assign("rs_draft", $this->m_kepeg_payroll_user->get_gaji_by_search($params));
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
            $this->tsession->unset_userdata("payroll_search");
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun")
            );
            $this->tsession->set_userdata("payroll_search", $params);
        }
        // redirect
        redirect("kepeg/payroll");
    }

    public function slip($FS_KD_PAYROLL = "", $FS_KD_PEG = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        //$search = $this->tsession->userdata('ipk_search');
        // search parameters
        //$periode = empty($search['periode']) ? '0' : $search['periode'];
        //$tahun = empty($search['tahun']) ? $year : $search['tahun'];
        $data['res']     = $this->m_kepeg_payroll->get_data_peg(array(date('Y-m-d'),$FS_KD_PEG));

        $data['results'] = $this->m_kepeg_payroll->getdata_payroll_perpegawai(array($FS_KD_PEG, $FS_KD_PAYROLL));
        
        $data['jk']      = $this->m_kepeg_payroll->get_jenis_kelamin($FS_KD_PEG);

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

        $data['results'] = $this->m_kepeg_payroll->getdata_payroll_perpegawai(array($FS_KD_PEG, $FS_KD_PAYROLL));
        $data['jk']      = $this->m_kepeg_payroll->get_jenis_kelamin($FS_KD_PEG);
     
        ob_start();
        $this->load->view('kepeg/payroll/slip_bonus', $data);
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
