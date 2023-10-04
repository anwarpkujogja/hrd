<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class account_setting extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();

        //load model
        $this->load->model('m_account');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function account() {
// set page rules
        $this->_set_page_rule("R");
// set template content
        $this->smarty->assign("template_content", "pengaturan/account_setting/account.html");
// get data account
        $result = $this->m_account->get_user_account($this->com_user['user_id']);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("user_name", $result['user_name']);
// notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
// output
        parent::display();
    }
    
     public function edit_account_process() {
// cek input
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required');
        $this->tnotification->set_rules('user_name_old', 'Username Lama', 'trim|required');
        $this->tnotification->set_rules('user_pass_old', 'Password Lama', 'trim|required');
        $this->tnotification->set_rules('user_pass_new', 'Password Baru', 'trim|required');
        $this->tnotification->set_rules('user_pass_confirm', 'Konfirm Password', 'trim|required');
// check username
        $username = $this->input->post('user_name');
        $username_old = $this->input->post('user_name_old');
        if ($username <> $username_old) {
            if ($this->m_account->is_exist_username($username)) {
                $this->tnotification->set_error_message("Username not available!");
            }
        }
        // check old password
        $password_old = $this->input->post('user_pass_old');
        if (!$this->m_account->is_exist_password($this->com_user['user_id'], $password_old)) {
            $this->tnotification->set_error_message("Wrong password!");
        } else {
            // check new password
            $password_new = $this->input->post('user_pass_new');
            $password_confirm = $this->input->post('user_pass_confirm');
            if ($password_new <> $password_confirm) {
                $this->tnotification->set_error_message("Password confirmation is not valid!");
            }
        }
// process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
//                $this->com_user['user_id'],
                $this->input->post('user_name'),
                $this->input->post('user_pass_new'),
                $this->com_user['user_id']
            );
// update
            if ($this->m_account->update_account($params)) {
// notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
// redirect
//                redirect("dashboard/operator/account/" . $this->com_user['user_id']);
            } else {
// default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
// default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
// default redirect
        redirect("pengaturan/account_setting/account");
    }

}