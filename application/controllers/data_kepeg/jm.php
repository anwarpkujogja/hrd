<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once(APPPATH . 'controllers/base/OperatorBase.php');

// --

class jm extends ApplicationBase
{

    // constructor
    public function __construct()
    {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_data_kepeg_jm');
        // load library
        $this->load->library('tnotification');
    }

    // list surat keluar
    public function index()
    {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/jm/index.html");
        // tahun
        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 4); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // bulan
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
        $search = $this->tsession->userdata('data_kepeg_jm_search');
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
        $params = array($bulan, $tahun);
        $this->smarty->assign("rs_result", $this->m_data_kepeg_jm->get_all_data_jm_periode($params));
        //$this->smarty->assign("tot_files", $this->m_event->get_all_event_limit($params));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
    // searching
    public function proses_cari()
    {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("data_kepeg_jm_search");
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun")
            );
            $this->tsession->set_userdata("data_kepeg_jm_search", $params);
        }
        // redirect
        redirect("data_kepeg/jm");
    }
    // edit surat masuk
    public function add()
    {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/jm/add.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // attachment
        $searchadd = $this->tsession->userdata('add_jm_search');
        if (!empty($searchadd)) {
            $this->smarty->assign("searchadd", $searchadd);
        } else {
            $searchadd['FS_KD_PEG'] = 'S000';
            $searchadd['tanggal'] = date('Y-m-d');
            $searchadd['tanggal2'] = date('Y-m-d');
            $this->smarty->assign("searchadd", $searchadd);
        }
        // assign periode
        $this->smarty->assign("FS_KD_PEG", $searchadd['FS_KD_PEG']);
        $this->smarty->assign("tanggal", $searchadd['tanggal']);
        $this->smarty->assign("tanggal2", $searchadd['tanggal2']);
        // search parameters
        $FS_KD_PEG = empty($searchadd['FS_KD_PEG']) ?: $searchadd['FS_KD_PEG'];
        $tanggal = empty($searchadd['tanggal']) ?: $searchadd['tanggal'];
        $tanggal2 = empty($searchadd['tanggal2']) ?: $searchadd['tanggal2'];

        $this->smarty->assign("rs_dokter", $this->m_data_kepeg_jm->get_data_dokter());
        $this->smarty->assign("result", $this->m_data_kepeg_jm->get_data_pegawai(array($FS_KD_PEG)));
        $this->smarty->assign("result_jm_all", $this->m_data_kepeg_jm->get_data_jm_all_pegawai(array($tanggal, $tanggal2, $tanggal, $tanggal2, $FS_KD_PEG)));


        //hitung jm
        $searchdata = $this->tsession->userdata('data_jm_search');

        $year_now = date('Y');
        for ($i = ($year_now); $i >= ($year_now - 0); $i--) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // bulan
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

        $year = date("Y");
        $month = date("m");

        if (!empty($searchdata)) {
            $this->smarty->assign("searchdata", $searchdata);
        } else {
            $searchdata['bulan'] = $month;
            $searchdata['tahun'] = $year;
            $this->smarty->assign("searchdata", $searchdata);
        }

        $this->smarty->assign("tahun", $searchdata['tahun']);
        $this->smarty->assign("bulan", $bulan[$searchdata['bulan']]);
        // assign periode
        $this->smarty->assign("FN_JM_ALL", $searchdata['FN_JM_ALL']);
        $this->smarty->assign("FN_JM_TAMBAH", $searchdata['FN_JM_TAMBAH']);
        $this->smarty->assign("FN_JM_TL", $searchdata['FN_JM_TL']);
        $this->smarty->assign("FN_JM_NON_PAJAK", $searchdata['FN_JM_NON_PAJAK']);

        $FN_JM_BRUTO = (($searchdata['FN_JM_ALL'] + $searchdata['FN_JM_TAMBAH']) - $searchdata['FN_JM_NON_PAJAK'] - $searchdata['FN_JM_TL']);
        $this->smarty->assign("FN_JM_BRUTO", $FN_JM_BRUTO);
        $FN_DASAR_POT = ($FN_JM_BRUTO * 0.5);
        $data["result_dasar_pot_tahun"] = $this->m_data_kepeg_jm->get_data_dasar_pot_tahun(array($FS_KD_PEG, $year));
        $this->smarty->assign("FN_DASAR_SBLM", $data["result_dasar_pot_tahun"]['DASAR_POT']);
        $this->smarty->assign("FN_DASAR_POT", $FN_DASAR_POT);
        $FN_DASAR_POT_KOM = ($FN_DASAR_POT + $data["result_dasar_pot_tahun"]['DASAR_POT']);
        $this->smarty->assign("FN_DASAR_POT_KOM", $FN_DASAR_POT_KOM);

        //status komulatif
        if ($data["result_dasar_pot_tahun"]['DASAR_POT'] <= '60000000') {
            $kom_sblm = '5';
        } elseif ($data["result_dasar_pot_tahun"]['DASAR_POT'] > '60000000' and $data["result_dasar_pot_tahun"]['DASAR_POT'] <= '250000000') {
            $kom_sblm = '15';
        } elseif ($data["result_dasar_pot_tahun"]['DASAR_POT'] > '250000000' and $data["result_dasar_pot_tahun"]['DASAR_POT'] <= '500000000') {
            $kom_sblm = '25';
        } elseif ($data["result_dasar_pot_tahun"]['DASAR_POT'] > '500000000') {
            $kom_sblm = '30';
        }

        if ($FN_DASAR_POT_KOM <= '60000000') {
            $kom_ssdh = '5';
        } elseif ($FN_DASAR_POT_KOM > '60000000' and $FN_DASAR_POT_KOM <= '250000000') {
            $kom_ssdh = '15';
        } elseif ($FN_DASAR_POT_KOM > '250000000' and $FN_DASAR_POT_KOM <= '500000000') {
            $kom_ssdh = '25';
        } elseif ($FN_DASAR_POT_KOM > '500000000') {
            $kom_ssdh = '30';
        }

        if (($kom_sblm == '5') and ($kom_ssdh == '5')) {
            $FN_PAJAK = ($FN_DASAR_POT * 0.05);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        } elseif (($kom_sblm == '15') and ($kom_ssdh == '15')) {
            $FN_PAJAK = ($FN_DASAR_POT * 0.15);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        } elseif (($kom_sblm == '25') and ($kom_ssdh == '25')) {
            $FN_PAJAK = ($FN_DASAR_POT * 0.25);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        } elseif (($kom_sblm == '30') and ($kom_ssdh == '30')) {
            $FN_PAJAK = ($FN_DASAR_POT * 0.3);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);

        } elseif (($kom_sblm == '5') and ($kom_ssdh == '15')) {
            $JM50 = $FN_JM_BRUTO * 0.5;

            $DASARPOT =  60000000 - $data["result_dasar_pot_tahun"]['DASAR_POT'];
            $PAJAK1 = $DASARPOT * 0.05;

            $KOMULATIF = $data["result_dasar_pot_tahun"]['DASAR_POT'] + $JM50;
            $DASARPOT2 = $KOMULATIF - 60000000;
            $PAJAK2 = $DASARPOT2 * 0.15;

            $FN_PAJAK = ($PAJAK1 + $PAJAK2);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);

        } elseif (($kom_sblm == '5') and ($kom_ssdh == '25')) {
            $JM50 = $FN_JM_BRUTO * 0.5;

            $DASARPOT =  250000000 - $data["result_dasar_pot_tahun"]['DASAR_POT'];
            $PAJAK1 = $DASARPOT * 0.05;

            $KOMULATIF = $data["result_dasar_pot_tahun"]['DASAR_POT'] + $JM50;
            $DASARPOT2 = $KOMULATIF - 250000000;
            $PAJAK2 = $DASARPOT2 * 0.25;

            $FN_PAJAK = ($PAJAK1 + $PAJAK2);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);

        } elseif (($kom_sblm == '5') and ($kom_ssdh == '30')) {
            $JM50 = $FN_JM_BRUTO * 0.5;

            $DASARPOT =  500000000 - $data["result_dasar_pot_tahun"]['DASAR_POT'];
            $PAJAK1 = $DASARPOT * 0.05;

            $KOMULATIF = $data["result_dasar_pot_tahun"]['DASAR_POT'] + $JM50;
            $DASARPOT2 = $KOMULATIF - 500000000;
            $PAJAK2 = $DASARPOT2 * 0.3;

            $FN_PAJAK = ($PAJAK1 + $PAJAK2);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        } elseif (($kom_sblm == '15') and ($kom_ssdh == '25')) {
            $JM50 = $FN_JM_BRUTO * 0.5;

            $DASARPOT =  250000000 - $data["result_dasar_pot_tahun"]['DASAR_POT'];
            $PAJAK1 = $DASARPOT * 0.15;

            $KOMULATIF = $data["result_dasar_pot_tahun"]['DASAR_POT'] + $JM50;
            $DASARPOT2 = $KOMULATIF - 250000000;
            $PAJAK2 = $DASARPOT2 * 0.25;

            $FN_PAJAK = ($PAJAK1 + $PAJAK2);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        } elseif (($kom_sblm == '15') and ($kom_ssdh == '30')) {
            $JM50 = $FN_JM_BRUTO * 0.5;

            $DASARPOT =  500000000 - $data["result_dasar_pot_tahun"]['DASAR_POT'];
            $PAJAK1 = $DASARPOT * 0.15;

            $KOMULATIF = $data["result_dasar_pot_tahun"]['DASAR_POT'] + $JM50;
            $DASARPOT2 = $KOMULATIF - 500000000;
            $PAJAK2 = $DASARPOT2 * 0.3;

            $FN_PAJAK = ($PAJAK1 + $PAJAK2);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        } elseif (($kom_sblm == '25') and ($kom_ssdh == '30')) {
            $JM50 = $FN_JM_BRUTO * 0.5;

            $DASARPOT =  500000000 - $data["result_dasar_pot_tahun"]['DASAR_POT'];
            $PAJAK1 = $DASARPOT * 0.25;

            $KOMULATIF = $data["result_dasar_pot_tahun"]['DASAR_POT'] + $JM50;
            $DASARPOT2 = $KOMULATIF - 500000000;
            $PAJAK2 = $DASARPOT2 * 0.3;

            $FN_PAJAK = ($PAJAK1 + $PAJAK2);
            $this->smarty->assign("FN_PAJAK", $FN_PAJAK);
        }

        if ($FN_DASAR_POT_KOM <= '60000000') {
            $FN_TARIF = '5%';
            $this->smarty->assign("FN_TARIF", $FN_TARIF);
        } elseif ($FN_DASAR_POT_KOM > '60000000' and $FN_DASAR_POT_KOM <= '250000000') {
            $FN_TARIF = '15%';
            $this->smarty->assign("FN_TARIF", $FN_TARIF);
        } elseif ($FN_DASAR_POT_KOM > '250000000' and $FN_DASAR_POT_KOM <= '500000000') {
            $FN_TARIF = '25%';
            $this->smarty->assign("FN_TARIF", $FN_TARIF);
        } elseif ($FN_DASAR_POT_KOM > '500000000') {
            $FN_TARIF = '30%';
            $this->smarty->assign("FN_TARIF", $FN_TARIF);
        }

        $FN_BAZAIS = ((($searchdata['FN_JM_ALL'] + $searchdata['FN_JM_TAMBAH']) - $searchdata['FN_JM_TL']) - $FN_PAJAK) * 0.025;
        $this->smarty->assign("FN_BAZAIS", $FN_BAZAIS);

        $this->smarty->assign("FN_POTONGAN", $searchdata['FN_POTONGAN']);
        $this->smarty->assign("FS_KET_POTONGAN", $searchdata['FS_KET_POTONGAN']);

        $FN_JM_NETTO = ($searchdata['FN_JM_ALL'] + $searchdata['FN_JM_TAMBAH']) - $searchdata['FN_JM_TL'] - $FN_PAJAK - $FN_BAZAIS - $searchdata['FN_POTONGAN'];
        $this->smarty->assign("FN_JM_NETTO", $FN_JM_NETTO);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    public function proses_cari_jm()
    {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata("add_jm_search");
            $this->tsession->unset_userdata("data_jm_search");
        } else {
            $params = array(
                "FS_KD_PEG" => $this->input->post("FS_KD_PEG"),
                "tanggal" => $this->input->post("tanggal"),
                "tanggal2" => $this->input->post("tanggal2")
            );
            $this->tsession->set_userdata("add_jm_search", $params);
        }
        // redirect
        redirect("data_kepeg/jm/add");
    }

    public function proses_generated_jm()
    {
        //set page rules
        $this->_set_page_rule("R");
        //data
        if ($this->input->post('savedata') == "Generate") {

            $params = array(
                "FN_JM_ALL" => $this->input->post("FN_JM_ALL"),
                "FN_JM_TAMBAH" => $this->input->post("FN_JM_TAMBAH"),
                "FN_JM_TL" => $this->input->post("FN_JM_TL"),
                "FN_JM_NON_PAJAK" => $this->input->post("FN_JM_NON_PAJAK"),
                "FN_POTONGAN" => $this->input->post("FN_POTONGAN"),
                "FS_KET_POTONGAN" => $this->input->post("FS_KET_POTONGAN"),
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun")
            );
            $this->tsession->set_userdata("data_jm_search", $params);
        } else {
            $this->tnotification->set_rules('FS_KD_PEG', 'Kode Pegawai', 'trim|required');
            //$this->tnotification->set_rules('FN_JM_ALL', 'Jasa Medis All', 'required');
            $this->tnotification->set_rules('FN_JM_TAMBAH', 'Jasa Medis Tambah', 'required');
            $this->tnotification->set_rules('FN_JM_TL', 'Jasa Medis TL', 'required');
            $this->tnotification->set_rules('FN_JM_BRUTO', 'Jasa Medis Bruto', 'required');
            $this->tnotification->set_rules('FN_DASAR_POT', 'Dasar Potongan Bruto', 'required');
            $this->tnotification->set_rules('FN_DASAR_POT_KOM', 'Dasar Potongan Progressif', 'required');
            $this->tnotification->set_rules('FN_PAJAK', 'Pajak', 'required');
            $this->tnotification->set_rules('FN_BAZAIS', 'Bazais', 'required');
            $this->tnotification->set_rules('FN_JM_NETTO', 'FN_JM_NETTO', 'required');
            $this->tnotification->set_rules('bulan', 'Bulan', 'required');
            $this->tnotification->set_rules('tahun', 'Tahun', 'required');
            if ($this->tnotification->run() !== FALSE) {
                $params = array(
                    $this->input->post('FS_KD_PEG'),
                    $this->input->post('FN_JM_ALL'),
                    $this->input->post('FN_JM_TAMBAH'),
                    $this->input->post('FN_JM_TL'),
                    $this->input->post('FN_JM_NON_PAJAK'),
                    $this->input->post('FN_JM_BRUTO'),
                    $this->input->post('FN_DASAR_POT'),
                    $this->input->post('FN_DASAR_POT_KOM'),
                    $this->input->post('FN_PAJAK'),
                    $this->input->post('FN_BAZAIS'),
                    $this->input->post('FN_POTONGAN'),
                    $this->input->post('FS_KET_POTONGAN'),
                    $this->input->post('FN_JM_NETTO'),
                    $this->input->post('bulan'),
                    $this->input->post('tahun'),
                    $this->input->post('tanggal'),
                    $this->input->post('tanggal2'),
                    date('Y-m-d'),
                    $this->com_user['user_name']
                );

                if ($this->m_data_kepeg_jm->insert($params)) {
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    $this->tsession->unset_userdata("data_jm_search");
                    $this->tsession->unset_userdata("add_jm_search");
                    redirect("data_kepeg/jm/add");
                } else {
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                    redirect("data_kepeg/jm/add");
                }
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
                redirect("data_kepeg/jm/add");
            }
        }
        // redirect
        redirect("data_kepeg/jm/add");
    }

    public function delete_process($FS_KD_TRS = "")
    {

        $params = array(
            date('Y-m-d'),
            $this->com_user['user_name'],
            $FS_KD_TRS
        );

        if ($this->m_data_kepeg_jm->delete($params)) {

            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }

        //default redirect
        redirect("data_kepeg/jm/");
    }

    public function email($FS_KD_TRS)
    {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "data_kepeg/jm/email.html");
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->assign("result", $this->m_data_kepeg_jm->get_data_jm_by_trs($FS_KD_TRS));
        //$this->smarty->assign("rs_sk", $this->m_data_kepeg_sk->get_sk());

        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    public function email_process($FS_KD_TRS)
    {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('email');
        $this->load->library('tupload');

        $this->tnotification->set_rules('FS_KD_TRS', 'Kode Transaksi', 'trim|required');

        if ($this->tnotification->run() !== FALSE) {
            //config email
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.googlemail.com';
            $config['smtp_port'] = '465';
            $config['smtp_timeout'] = '7';
            $config['smtp_user'] = '#';
            $config['smtp_pass'] = '#';
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['mailtype'] = 'text'; // or html
            $config['validation'] = TRUE; // bool whether to validate email or not     
            $config['mailtype'] = 'html';

            $this->email->initialize($config);
            if ((!empty($_FILES['att_name1']['tmp_name'])) && (!empty($_FILES['att_name2']['tmp_name']))) {
                $name = $_FILES['att_name1']['name'];
                $filename1 = preg_replace('/\s+/', '_', $name);
                $name2 = $_FILES['att_name2']['name'];
                $filename2 = preg_replace('/\s+/', '_', $name2);
                $config['upload_path'] = 'resource/doc/jm/';
                $config['allowed_types'] = 'pdf';
                $this->tupload->initialize($config);

                //process upload
                if ($this->tupload->do_upload('att_name1') && $this->tupload->do_upload('att_name2')) {
                    //send email
                    $mail = $this->m_data_kepeg_jm->get_data_jm_by_trs($this->input->post('FS_KD_TRS'));
                    $html = "<b>Bp/Ibu ".$mail['FS_NM_PEG']."</b> Berikut kami kirimkan Laporan Jasa Medis RS PKU Muhammadiyah Gamping";
                    /*$html .= "Perihal : " . $this->input->post('surat_perihal'). '<br />';
                $html .= "Kepada : " . $asal_surat['jabatan_nama']. '<br />';
                $html .= "Asal Surat :" .$asal_surat['instansi_name']. '<br />';
                $html .= "Untuk Eksekusi Surat Buka Laman www.pkugamping.com/eoffice";*/
                    // send
                    $this->email->from('kepegawaian.pkugamping@gmail.com', 'Keuangan RS PKU Muhammadiyah Gamping - Email by System');
                    $this->email->to($mail['FS_EMAIL']);
                    $this->email->subject('Laporan Jasa Medis RS PKU Muhammadiyah Gamping');
                    $this->email->message($html);
                    $this->email->attach('resource/doc/jm/' .$filename1);
                    $this->email->attach('resource/doc/jm/' .$filename2);
                    $this->email->send();

                    unlink('resource/doc/jm/' . $filename1);
                    unlink('resource/doc/jm/' . $filename2);

                } else {
                    $this->tnotification->set_error_message($this->tupload->display_errors());
                    redirect("data_kepeg/jm/email/".$this->input->post('FS_KD_TRS'));
                }
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
            redirect("data_kepeg/jm/email/".$this->input->post('FS_KD_TRS'));
        }

        $this->tnotification->delete_last_field();
        $this->tnotification->sent_notification("success", "Detail berhasil disimpan");
        redirect("data_kepeg/jm");
    }
}
