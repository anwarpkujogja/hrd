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
        // load model
        $this->load->model('m_operator');
        // load library
        $this->load->library('tnotification');
    }

    // data pribadi
    public function data_pribadi() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "dashboard/account_setting/index.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        // get data pribadi
        //$result = $this->m_account->get_data_pribadi($this->com_user['user_id']);
        // folder image img
        //$img_path = 'resource/doc/images/users/' . $result['user_img'];
        // jika tidak ditemukan file, pakai image default
        /*if (!is_file($img_path)) {
            $img_path = 'resource/doc/images/users/default.png';
        }
        if (!empty($result)) {
            $this->smarty->assign("result", $result);
        }
        // images
        $this->smarty->assign("image_path", base_url() . $img_path);*/
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit users process
    public function process_data_pribadi() {
        //cek input
        $this->tnotification->set_rules('user_id', 'User ID', 'trim');
        $this->tnotification->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required');
        $this->tnotification->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
        $this->tnotification->set_rules('tmp_lahir', 'Tempat Lahir', 'trim|required');
        $this->tnotification->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required');
        $this->tnotification->set_rules('alamat', 'Nama Lengkap', 'trim|required');
        $this->tnotification->set_rules('no_telp', 'Telepon', 'trim');
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|valid_mail');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $param_mail = array(
                $this->input->post('user_mail'),
                $this->com_user['user_id'],
                $this->input->post('user_id')
            );
            if (!$this->m_account->update_email_users($param_mail)) {
                // email error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
                redirect('pengaturan/operator/edit/' . $this->input->post('user_id'));
            }
            $params = array(
                $this->input->post('nama_lengkap'),
                $this->input->post('jenis_kelamin'),
                $this->input->post('tmp_lahir'),
                $this->input->post('tgl_lahir'),
                $this->input->post('alamat'),
                $this->input->post('no_telp'),
                $this->com_user['user_id'],
                $this->com_user['user_id']
            );
//            //insert
            if ($this->m_account->update_data_pribadi($params)) {
                // upload image
                if (!empty($_FILES['user_img']['tmp_name'])) {
                    // load
                    $this->load->library('tupload');
                    // unlink old file
                    $filepath = 'resource/doc/images/users/' . $this->com_user['user_img'];
                    if (is_file($filepath)) {
                        unlink($filepath);
                    }
                    $ext = pathinfo($_FILES['user_img']['name'], PATHINFO_EXTENSION);
                    // mengganti filename dengan user_id + extensi
                    $filename = $this->com_user['user_id'] . "." . $ext;
                    // upload config
                    $config['upload_path'] = 'resource/doc/images/users/';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['max_size'] = '2048';
                    $config['max_width'] = '1024';
                    $config['max_height'] = '768';
                    $config['file_name'] = $filename;
                    $config['overwrite'] = TRUE;
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload_image('user_img', 200, 200)) {
                        $data = $this->tupload->data();
                        $this->m_account->update_nama_file(array($data['file_name'], $this->com_user['user_id']));
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->set_error_message($this->tupload->display_errors());
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("dashboard/account_setting/data_pribadi");
    }

// 
//-----------ACCOUNT SETTING------------
//
    public function account() {
// set page rules
        $this->_set_page_rule("R");
// set template content
        $this->smarty->assign("template_content", "dashboard/account_setting/account.html");
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

// edit process
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
        redirect("dashboard/account_setting/account");
    }

    // data pribadi
    public function change_role() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "dashboard/change_role/index.html");

        // get data pribadi
        $session = $this->tsession->userdata('session_arsip');
        $result = $this->m_account->get_data_pribadi_ch(array($this->com_user['user_id'], $session['role_id']));
        $this->smarty->assign("result", $result);
        $rs_role = $this->m_account->role_aktif($this->com_user['user_id']);
        $this->smarty->assign("rs_role", $rs_role);
        // get data account
        $result2 = $this->m_operator->get_user_account($this->com_user['user_id']);
//        echo '<pre>';print_r($result);exit();
        // password
        foreach ($result2 as $data) {
            $data['user_pass'] = $this->encrypt->decode($data['user_pass'], $data['user_key']);
            $this->smarty->assign("result2", $data);
        }
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // logout process
    public function logout_process() {
        // user id
        $user_id = $this->tsession->userdata('session_arsip');
        // insert logout time
        $this->m_account->update_user_logout($user_id);
        // unset session
        $this->tsession->unset_userdata('session_arsip');
        // output
        redirect('dashboard/account_setting/change_role_process');
    }

    // login process
    public function change_role_process() {
        // set rules
        $this->tnotification->set_rules('username', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('pass', 'Password', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('role_id', 'Role', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {

            // params
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('pass'));
            $role_id = $this->input->post('role_id');
            $user_id = $this->input->post('user_id');
            // update baru
            //$get_user_id = $this->m_operator->get_user_id(array($this->input->post('role_id'), $this->input->post('user_id')));
            // set session
            $this->tsession->unset_userdata('session_arsip');
            $this->tsession->set_userdata('session_arsip', array('user_id' => $user_id, 'role_id' => $role_id));
        }
        // output
        redirect('login/operatorlogin');
    }

}
