<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class data_keluarga extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_data_keluarga');
        // load library
        $this->load->library('tnotification');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "umum/data_keluarga/index.html");
       
        $start = $this->uri->segment(4, 0) + 1;
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $data = $this->com_user['user_name'] .'%';
        $this->smarty->assign("rs_kel", $this->m_data_keluarga->get_all_keluarga($data));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
   

    // edit
    public function add($id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "umum/data_keluarga/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get data
        $start = 1;
        $this->smarty->assign("no", $start);
        $this->smarty->assign("result", $this->m_data_keluarga->get_all_keluarga_by_id($id));
        $this->smarty->assign("rs_att", $this->m_data_keluarga->get_all_att_by_id($id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");

        $this->load->library('tupload',$config);
        // cek input
        $this->tnotification->set_rules('FS_KD_PEG', 'Kode Pegawai', 'required');
        $this->tnotification->set_rules('FS_NM_FILE_PEG_KET', 'Keterangan Pegawai', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('FS_KD_PEG'),
                $this->input->post('FS_NM_FILE_PEG_KET'),
                $this->com_user['user_id']
            );
            // insert
            if ($this->m_data_keluarga->insert($params)) {
                $FS_KD_TRS = $this->m_data_keluarga->get_last_inserted_id();
                
                if (!empty($_FILES['att_name']['tmp_name'])) {
                    $config['encrypt_name'] = TRUE;
                    $filename = preg_replace('/\s+/', '_',time().$_FILES["att_name"]['name']);
                    $config['file_name'] =  $filename;
                    $filesize = $_FILES['att_name']['size'];
                    $config['upload_path'] = 'resource/doc/arsip/';
                    $config['allowed_types'] = 'pdf';
                    $this->tupload->initialize($config);
    
                    //process upload
                    if ($this->tupload->do_upload('att_name')) {
                        $data = $this->tupload->data();
// insert filename
                        $params = array($filename, $FS_KD_TRS);
                        $this->m_data_keluarga->update_att($params);
                    } else {
// jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
                $this->tnotification->display_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                redirect("umum/data_keluarga/add/" . $this->input->post('FS_KD_PEG'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("umum/data_keluarga/add/" . $this->input->post('FS_KD_PEG'));
    }

    // hapus process
    public function delete_process($FS_KD_FILE_PEG="",$FS_KD_PEG="",$FS_NM_FILE_PEG="") {
        // set page rules
        $this->_set_page_rule("D");
        // process
            $params = array($FS_KD_FILE_PEG);
            // insert
            if ($this->m_data_keluarga->delete($FS_KD_FILE_PEG)) {
                unlink("resource/doc/data_keluarga/".$FS_NM_FILE_PEG);
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("umum/data_keluarga/add/" . $FS_KD_PEG);
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        
        // default redirect
        redirect("umum/data_keluarga/add/" . $FS_KD_PEG);
    }

    // add data
    public function upload_process() {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('event_id', 'ID Kegiatan', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get event_id
            $event_id = $this->input->post('event_id');
            // upload config
            if (!empty($_FILES['file_name']['tmp_name'])) {
                $config['upload_path'] = 'resource/doc/arsip/';
                $config['allowed_types'] = 'pdf';
                // modifikasi disini
                // looping $_FILES dan buat array baru
                foreach ($_FILES['file_name'] as $key => $val) {
                    $i = 1;
                    foreach ($val as $v) {
                        $field_name = "file_" . $i;
                        $_FILES[$field_name][$key] = $v;
                        $i++;
                    }
                }
                $this->tupload->initialize($config);
                // hapus array awal, karena kita sudah memiliki array baru
                unset($_FILES['file_name']);
                $error = array();
                $data_upload = array();
                //--
                foreach ($_FILES as $field_name => $file) {
                    if (!$this->tupload->do_upload($field_name)) {
                        $error[$file['name']] = $this->tupload->display_errors();
                    } else {
                        $data_upload[] = $this->tupload->data();
                    }
                }
                //data upload
                for ($i = 0; $i < count($data_upload); $i++) {
                    $params = array($event_id, $data_upload[$i]['file_name'], $data_upload[$i]['file_size'], $this->com_user['user_id']);

                    $this->m_event->insert_attachment($params);
                }
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "File berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "File gagal disimpan");
        }
        // default redirect
        redirect("surat/event/upload/" . $event_id);
    }

    // download attachment
    public function download($surat_name = "") {
        $surat_path = 'resource/doc/data_keluarga/' . $surat_name;
        if (is_file($surat_path)) {
            header('Content-Description: Download File');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($surat_path));
            header('Content-Disposition: attachment; suratname="' . $surat_name . '"');
            readfile($surat_path);
            exit();
        }
    }
}