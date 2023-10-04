<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class jm extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_lap_jm');
        // load library
        $this->load->library('tnotification');
    }

    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "laporan/jm/index.html");
        // tahun
        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        
        // default search
        $year = date("Y");
        // get search parameter
        $search = $this->tsession->userdata('lap_jm_search');
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        } else {
            $search['FS_KD_PEG'] = 'S000';
            $search['tahun'] = $year;
            $this->smarty->assign("search", $search);
        }
        // assign periode
        $this->smarty->assign("tahun", $search['tahun']);
        // search parameters
        $FS_KD_PEG = empty($search['FS_KD_PEG']) ? $year : $search['FS_KD_PEG'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];
        // pagination
        $config['uri_segment'] = 4;
        $config['per_page'] = 50;
        
        // pagination attribute
        $start = $this->uri->segment(4, 0) + 1;
        $end = $this->uri->segment(4, 0) + $config['per_page'];
        $end = (($end > $config['total_rows']) ? $config['total_rows'] : $end);

        // pagination assign value
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $params = array($FS_KD_PEG,$tahun);
        $this->smarty->assign("rs_dokter", $this->m_lap_jm->get_data_dokter());
        $this->smarty->assign("rs_result", $this->m_lap_jm->get_all_data_jm_periode($params));
        //notification
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
            $this->tsession->unset_userdata("lap_jm_search");
        } else {
            $params = array(
                "FS_KD_PEG" => $this->input->post("FS_KD_PEG"),
                "tahun" => $this->input->post("tahun")
            );
            $this->tsession->set_userdata("lap_jm_search", $params);
        }
        // redirect
        redirect("laporan/jm");
    }

    public function detil($FD_PERIODE_AWAL="",$FD_PERIODE_AKHIR="",$FS_KD_PEG = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        $now = date('Y-m-d');
        $data['rs_dokter'] = $this->m_lap_jm->get_data_dokter2(array($FS_KD_PEG,$FD_PERIODE_AWAL,$FD_PERIODE_AKHIR));
        $data["rs_result"] = $this->m_lap_jm->get_data_jm_by_rg(array($FD_PERIODE_AWAL,$FD_PERIODE_AKHIR,$FD_PERIODE_AWAL,$FD_PERIODE_AKHIR,$FS_KD_PEG));
        ob_start();
        $this->load->view('laporan/jm/detil', $data);
        $content = ob_get_contents();
        ob_end_clean();

        try {
            $html2pdf = new HTML2PDF('L', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            $html2pdf->Output('detil_jm.pdf','FI');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }

    public function rekap($FS_KD_TRS = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        $now = date('Y-m-d');
        $data['rs_dokter'] = $this->m_lap_jm->get_data_dokter_by_trs(array($FS_KD_TRS));
        $data["result"] = $this->m_lap_jm->get_data_jm_by_trs(array($FS_KD_TRS));
        ob_start();
        $this->load->view('laporan/jm/rekap', $data);
        $content = ob_get_contents();
        ob_end_clean();

        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            $html2pdf->Output('rekap_jm.pdf','FI');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }

}