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
        $this->load->model('m_ipk');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/ipk/list.html");
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
        $bagian = empty($search['bagian']) ? '%' : '%'.$search['bagian'].'%';
        $periode = empty($search['periode']) ? '0' : $search['periode'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $params = array($bagian,$periode, $tahun);
        $params2 = array($periode, $tahun);
        $this->smarty->assign("rs_draft", $this->m_ipk->get_ipk_by_search($params));
        $this->smarty->assign("rs_bagian", $this->m_ipk->get_bagian());
        $this->smarty->assign("rs_jmlindex", $this->m_ipk->get_jmlipk_by_search($params2));
        $this->smarty->assign("rs_jmlpdpt", $this->m_ipk->get_jmlpdpt_by_search($params2));
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
                "bagian" => $this->input->post("bagian"),
                "periode" => $this->input->post("periode"),
                "tahun" => $this->input->post("tahun")
            );
            $this->tsession->set_userdata("ipk_search", $params);
        }
        // redirect
        redirect("data_kepeg/ipk");
    }
    // detail surat
    public function add($nota_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/ipk/detail.html");
        $search = $this->tsession->userdata('ipk_detail_search');
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

        $FS_KD_PEG = empty($search['FS_KD_PEG']) ? '' : $search['FS_KD_PEG'];

        $params = array($FS_KD_PEG);
        $this->smarty->assign("result", $this->m_ipk->get_data_peg_by_search($params));
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
            $this->tsession->unset_userdata("ipk_detail_search");
        } else {
            $params = array(
                "FS_KD_PEG" => $this->input->post("FS_KD_PEG")
            );
            $this->tsession->set_userdata("ipk_detail_search", $params);
        }
        // redirect
        redirect("data_kepeg/ipk/add");
    }

    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $cek_data = $this->m_ipk->cek_data_input(array($this->input->post('FS_KD_PEG'), $this->input->post('periode'), $this->input->post('tahun')));
        if ($cek_data == '0') {
            // users
            $this->tnotification->set_rules('FS_KD_PEG', 'Kode Pegawai', 'trim|required');
            $this->tnotification->set_rules('FS_PENDIDIKAN', 'Pendidikan', 'trim|required');
            $this->tnotification->set_rules('FS_JABATAN', 'Jabatan', 'trim|required');
            $this->tnotification->set_rules('FS_BAGIAN', 'Bagian', 'trim|required');
            $this->tnotification->set_rules('FS_MASA_KERJA', 'Masa Kerja', 'trim|required');
            $this->tnotification->set_rules('FS_TUGAS_LAIN', 'Tugas Lain', 'trim|required');
            $this->tnotification->set_rules('FS_IKU', 'IKU', 'trim|required');
            $this->tnotification->set_rules('FS_IKI', 'IKI', 'trim|required');
            $this->tnotification->set_rules('FS_PENGURANG', 'Pengurang', 'trim|required');
            //proses
            if ($this->tnotification->run() !== FALSE) {
                $params = array(
                    $this->input->post('FS_KD_PEG'),
                    $this->input->post('FS_PENDIDIKAN'),
                    $this->input->post('FS_JABATAN'),
                    $this->input->post('FS_BAGIAN'),
                    $this->input->post('FS_MASA_KERJA'),
                    $this->input->post('FS_TUGAS_LAIN'),
                    $this->input->post('FS_IKU'),
                    $this->input->post('FS_IKI'),
                    $this->input->post('FS_PENGURANG'),
                    $this->input->post('FS_PENGURANG_KET'),
                    $this->input->post('periode'),
                    $this->input->post('tahun'),
                    $this->input->post('FS_PENDIDIKAN') + $this->input->post('FS_JABATAN') + $this->input->post('FS_BAGIAN') + $this->input->post('FS_MASA_KERJA') + $this->input->post('FS_TUGAS_LAIN'),
                    $this->input->post('FS_POTONGAN'),
                    $this->com_user['user_name'],
                    date('Y-m-d'),
                    date('H:i:s')
                );
                //insert
                if ($this->m_ipk->insert($params)) {
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
            redirect("data_kepeg/ipk/add/");
        } else {
            $this->tnotification->sent_notification("error", "Data dengan NIK dan Periode yang sama sudah pernah di entry");
            redirect("data_kepeg/ipk/add/");
        }
    }

    public function add_pendapatan() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/ipk/add_pendapatan.html");

        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 1); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // output
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        parent::display();
    }

    public function add_pendapatan_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $cek_data = $this->m_ipk->cek_data_input_jml(array($this->input->post('periode'), $this->input->post('tahun')));
        if ($cek_data == '0') {
            // users
            $this->tnotification->set_rules('periode', 'Periode', 'trim|required');
            $this->tnotification->set_rules('tahun', 'Tahun', 'trim|required');
            $this->tnotification->set_rules('FN_JML_PENDAPATAN', 'Pendapatan', 'trim|required');
            //proses
            if ($this->tnotification->run() !== FALSE) {
                $params = array(
                    $this->input->post('periode'),
                    $this->input->post('tahun'),
                    $this->input->post('FN_JML_PENDAPATAN'),
                    $this->com_user['user_name'],
                    date('Y-m-d'),
                    date('H:i:s')
                );
                //insert
                if ($this->m_ipk->insert_pendapatan($params)) {
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
            redirect("data_kepeg/ipk/");
        } else {
            $this->tnotification->sent_notification("error", "Data dengan Periode yang sama sudah pernah di entry");
            redirect("data_kepeg/ipk/");
        }
    }

    public function slip($FS_KD_TRS = "") {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        $search = $this->tsession->userdata('ipk_search');
        // search parameters
        $periode = empty($search['periode']) ? '0' : $search['periode'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];
        
        $data['result'] = $this->m_ipk->get_data_peg_by_search_trs($FS_KD_TRS);
        $data["rs_jmlindex"]= $this->m_ipk->get_jmlipk_by_search(array($periode,$tahun));
        $data["rs_jmlpdpt"]= $this->m_ipk->get_jmlpdpt_by_search(array($periode,$tahun));
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
    
    public function slip_all() {
        $this->_set_page_rule("R");
        $this->load->library('html2pdf');
        $search = $this->tsession->userdata('ipk_search');
        // search parameters
        $bagian = empty($search['bagian']) ? '%' : '%'.$search['bagian'].'%';
        $periode = empty($search['periode']) ? '0' : $search['periode'];
        $tahun = empty($search['tahun']) ? $year : $search['tahun'];
        
        $data['rs_result'] = $this->m_ipk->get_data_peg_by_search_all(array($bagian,$periode,$tahun));
        $data["rs_jmlindex"]= $this->m_ipk->get_jmlipk_by_search(array($periode,$tahun));
        $data["rs_jmlpdpt"]= $this->m_ipk->get_jmlpdpt_by_search(array($periode,$tahun));
        ob_start();
        $this->load->view('data_kepeg/ipk/slip_all', $data);
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

    public function edit($FS_KD_TRS = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/ipk/edit.html");
        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        $this->smarty->assign("result", $this->m_ipk->get_data_peg_by_search_trs($FS_KD_TRS));
        // output
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        parent::display();
    }
    
     public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
            // users
            $this->tnotification->set_rules('FS_PENDIDIKAN', 'Pendidikan', 'trim|required');
            $this->tnotification->set_rules('FS_JABATAN', 'Jabatan', 'trim|required');
            $this->tnotification->set_rules('FS_BAGIAN', 'Bagian', 'trim|required');
            $this->tnotification->set_rules('FS_MASA_KERJA', 'Masa Kerja', 'trim|required');
            $this->tnotification->set_rules('FS_TUGAS_LAIN', 'Tugas Lain', 'trim|required');
            $this->tnotification->set_rules('FS_IKU', 'IKU', 'trim|required');
            $this->tnotification->set_rules('FS_IKI', 'IKI', 'trim|required');
            $this->tnotification->set_rules('FS_PENGURANG', 'Pengurang', 'trim|required');
            //proses
            if ($this->tnotification->run() !== FALSE) {
                $params = array(
                    $this->input->post('FS_PENDIDIKAN'),
                    $this->input->post('FS_JABATAN'),
                    $this->input->post('FS_BAGIAN'),
                    $this->input->post('FS_MASA_KERJA'),
                    $this->input->post('FS_TUGAS_LAIN'),
                    $this->input->post('FS_IKU'),
                    $this->input->post('FS_IKI'),
                    $this->input->post('FS_PENGURANG'),
                    $this->input->post('FS_PENGURANG_KET'),
                    $this->input->post('periode'),
                    $this->input->post('tahun'),
                    $this->input->post('FS_PENDIDIKAN') + $this->input->post('FS_JABATAN') + $this->input->post('FS_BAGIAN') + $this->input->post('FS_MASA_KERJA') + $this->input->post('FS_TUGAS_LAIN'),
                    $this->input->post('FS_POTONGAN'),
                    $this->com_user['user_name'],
                    date('Y-m-d'),
                    date('H:i:s'),
                    $this->input->post('FS_KD_TRS')
                    
                );
                //insert
                if ($this->m_ipk->update($params)) {
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
            redirect("data_kepeg/ipk/edit/".$this->input->post('FS_KD_TRS'));
        
    }
}
