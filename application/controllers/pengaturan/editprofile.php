<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class editprofile extends ApplicationBase {

    // constructor
    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model('m_operator');
        $this->load->model('m_settings');
        // load library
        $this->load->library('encrypt');
        $this->load->library('tnotification');
        $this->load->library('pagination');
        // global assign
        $this->smarty->assign("encrypt", $this->encrypt);
    }

    public function index() {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/user_info/editprofile.html");
        // get data
        $result = $this->m_operator->get_detail_operator_by_id($this->com_user['user_id']);
        if (!empty($result)) {
            $result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            $this->smarty->assign("result", $result);
            $rs_users = $this->m_operator->get_detail_users_by_id($this->com_user['user_id']);
            $this->smarty->assign("rs_users", $rs_users);
        } else {
            redirect('admin/adminwelcome');
        }
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('nama_lengkap', 'Nama Operator', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('user_mail', 'User Email', 'trim|required|valid_email|max_length[50]');
        $this->tnotification->set_rules('user_name', 'User Name', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('user_id', 'user ID', 'trim');
        
        $this->tnotification->set_rules('no_telp', 'Operator Phone', 'trim');
        $this->tnotification->set_rules('alamat', 'Operator Notes', 'trim');
        
        // message error
        $this->tnotification->set_message('required', '%s harus diisi');
        // get data
        $user = $this->m_operator->get_detail_operator_by_id($this->com_user['user_id']);
        // check email
        $email = trim($this->input->post('user_mail'));
        if ($this->input->post('user_mail') != $user['user_mail']) {
            if ($this->m_operator->is_exist_email($email)) {
                $this->tnotification->set_error_message('Email tidak tersedia');
            }
        }
        // check username
        $username = trim($this->input->post('user_name'));
        if ($this->input->post('user_name') != $user['user_name']) {
            if ($this->m_operator->is_exist_username($username)) {
                $this->tnotification->set_error_message('Username tidak tersedia');
            }
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $password_key = crc32($this->input->post('user_pass'));
            $password = $this->encrypt->encode($this->input->post('user_pass'), $password_key);
            // parameter
            $params = array(
                $this->input->post('user_name'), $password, $password_key,
                $this->input->post('user_mail'),
                $this->com_user['user_id'], $this->com_user['user_id']
            );
            // parameter
            $params_users = array(
                $this->input->post('nama_lengkap'),
                $this->input->post('alamat'),
                $this->input->post('no_telp'),
                $this->com_user['user_id'],
                $this->com_user['user_id']
            );
            // update
            if ($this->m_operator->update_operator($params)&&$this->m_operator->update_user_detail($params_users)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/editprofile");
    }

}