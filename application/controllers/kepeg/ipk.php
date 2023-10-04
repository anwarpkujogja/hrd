<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class ipk extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_kepeg_ipk');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "kepeg/ipk/list.html");
        // tahun
        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // default search
        $year = date("Y");
        // get search parameter
        $search = $this->tsession->userdata('ipk_search');
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        } else {
            $search['tahun'] = $year;
            $this->smarty->assign("search", $search);
        }
        // assign periode
        $this->smarty->assign("periode", $search['periode']);
        $this->smarty->assign("tahun", $search['tahun']);
        // search parameters
        $periode = empty($search['periode']) ? '0' : $search['periode'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $params = array($this->com_user['user_name'],$periode, $tahun);
       $this->smarty->assign("rs_draft", $this->m_kepeg_ipk->get_ipk_by_search($params));
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
            $this->tsession->unset_userdata("ipk_search");
        } else {
            $params = array(
                "periode" => $this->input->post("periode"),
                "tahun" => $this->input->post("tahun")
            );
            $this->tsession->set_userdata("ipk_search", $params);
        }
        // redirect
        redirect("kepeg/ipk");
    }

    public function slip($FS_KD_TRS = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        $search = $this->tsession->userdata('ipk_search');
        // search parameters
        $periode = empty($search['periode']) ? '0' : $search['periode'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];
        
        $data['result'] = $this->m_kepeg_ipk->get_data_peg_by_search_trs($FS_KD_TRS);
        $data["rs_jmlindex"]= $this->m_kepeg_ipk->get_jmlipk_by_search(array($periode,$tahun));
        $data["rs_jmlpdpt"]= $this->m_kepeg_ipk->get_jmlpdpt_by_search(array($periode,$tahun));
        ob_start();
        $this->load->view('data_kepeg/ipk/slip', $data);
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
