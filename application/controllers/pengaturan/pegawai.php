<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class pegawai extends ApplicationBase {

    //contructor
    public function __construct() {
        //parent contructor
        parent::__construct();
        //load model
        $this->load->model('m_operator');
        $this->smarty->assign("m_operator", $this->m_operator);
        //load library
        $this->load->library('encrypt');
        $this->load->library('pagination');
        $this->load->library('tnotification');
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
    }

    //list view
    public function index() {
        //set rule
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/pegawai/list.html");
        // pagination
        $config['base_url'] = site_url("pengaturan/pegawai/index/");
        $config['total_rows'] = $this->m_operator->get_total_users_pegawai();
        $config['uri_segment'] = 4;
        $config['per_page'] = 50;
        $this->pagination->initialize($config);
        $pagination['data'] = $this->pagination->create_links();
        // pagination attribute
        $start = $this->uri->segment(4, 0) + 1;
        $end = $this->uri->segment(4, 0) + $config['per_page'];
        $end = (($end > $config['total_rows']) ? $config['total_rows'] : $end);
        $pagination['start'] = ($config['total_rows'] == 0) ? 0 : $start;
        $pagination['end'] = $end;
        $pagination['total'] = $config['total_rows'];
        // pagination assign value
        $this->smarty->assign("pagination", $pagination);
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list data
        $params = array(($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_operator->get_all_users_limit_pegawai($params));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/pegawai/add.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        // get list
        $result = $this->m_operator->get_all_users();
        if (!empty($result)) {
            //$result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            $this->smarty->assign("result", $result);
        }
        // get list data
        $list_unit = $this->m_operator->get_list_unit();
        $this->smarty->assign("rs_unit", $list_unit);
        $this->smarty->assign("rs_jab", $this->m_operator->get_all_jabatan());
        $this->smarty->assign("rs_gol", $this->m_operator->get_all_golongan());
        // images
        $img_path = 'resource/doc/images/users/';
        if (!is_file($img_path)) {
            $img_path = 'resource/doc/temp/';
        }
        $this->smarty->assign("image_path", base_url() . $img_path);
        // get role name
        $this->smarty->assign("role_list", $this->m_operator->get_all_role());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        // users
        $this->tnotification->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('nip', 'NIP', 'trim|required|maxlength[20]');
        $this->tnotification->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required');
        $this->tnotification->set_rules('tmp_lahir', 'Tempat Lahir', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required');
        $this->tnotification->set_rules('unit_id', 'ID Unit', 'trim');
        $this->tnotification->set_rules('alamat', 'Alamat', 'trim|maxlength[255]');
        $this->tnotification->set_rules('no_telp', 'Telepon', 'trim|maxlength[20]');
        $this->tnotification->set_rules('jabatan_id', 'Jabatan', 'trim|required');
        $this->tnotification->set_rules('golongan_id', 'Pangkat/Gol', 'trim|required');
        // com_user
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]');
        // check email
        $email = trim($this->input->post('user_mail'));
        if ($this->m_operator->is_exist_email($email)) {
            $this->tnotification->set_error_message('Email is not available');
        }
        // check username
        $username = trim($this->input->post('user_name'));
        if ($this->m_operator->is_exist_username($username)) {
            $this->tnotification->set_error_message('Username is not available');
        }

        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
               '',
                '',
                '',
                $this->input->post('user_mail'),
                '0',
                $this->com_user['user_id']
            );
            //insert
            if ($this->m_operator->insert($params)) {
                $user_id = $this->m_operator->get_last_inserted_id();
                $params = array(
                    $user_id,
                    $this->input->post('unit_id'),
                    $this->input->post('nip'),
                    $this->input->post('nama_lengkap'),
                    $this->input->post('jenis_kelamin'),
                    $this->input->post('tmp_lahir'),
                    $this->input->post('tgl_lahir'),
                    $this->input->post('no_telp'),
                    $this->input->post('alamat'),
                    $this->input->post('jabatan_id'),
                    $this->input->post('golongan_id'),
                    $this->com_user['user_id']
                );
                $this->m_operator->insert_users($params);
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
        redirect("pengaturan/pegawai/add");
    }

    public function edit($id = '') {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/pegawai/edit.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        // get list
        // folder image img
        $img_path = 'resource/doc/images/users/';
        $result = $this->m_operator->get_users_by_id($id);
        // jika tidak ditemukan file, pakai image default
        if (!is_file($img_path . $result['user_img'])) {
            $result['user_img'] = 'default.png';
        }
        if (!empty($result)) {
//            $result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            $this->smarty->assign("result", $result);
        }
        // get list unit
        $list_unit = $this->m_operator->get_list_unit();
        $this->smarty->assign("rs_unit", $list_unit);
        $this->smarty->assign("rs_jab", $this->m_operator->get_all_jabatan());
        $this->smarty->assign("rs_gol", $this->m_operator->get_all_golongan());
        // images
        $this->smarty->assign("image_path", base_url() . $img_path);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit users process
    public function edit_process() {
        //set rule
        $this->_set_page_rule("U");
        //cek input
        $this->tnotification->set_rules('user_id', 'User ID', 'trim|required');
        $this->tnotification->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('nip', 'NIP', 'trim|required|maxlength[20]');
        $this->tnotification->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required');
        $this->tnotification->set_rules('tmp_lahir', 'Tempat Lahir', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required');
        $this->tnotification->set_rules('unit_id', 'ID Unit', 'trim');
        $this->tnotification->set_rules('alamat', 'Alamat', 'trim|maxlength[255]');
        $this->tnotification->set_rules('no_telp', 'Telepon', 'trim|maxlength[20]');
        $this->tnotification->set_rules('jabatan_id', 'Jabatan', 'trim|required');
        $this->tnotification->set_rules('golongan_id', 'Pangkat/Gol', 'trim|required');
        // com_user
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]');
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $param_mail = array(
                $this->input->post('user_mail'),
                $this->com_user['user_id'],
                $this->input->post('user_id')
            );
            if (!$this->m_operator->update_email_users($param_mail)) {
                // email error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
                redirect('pengaturan/operator/edit/' . $this->input->post('user_id'));
            }
            $params = array(
                $this->input->post('unit_id'),
                $this->input->post('nip'),
                $this->input->post('nama_lengkap'),
                $this->input->post('jenis_kelamin'),
                $this->input->post('tmp_lahir'),
                $this->input->post('tgl_lahir'),
                $this->input->post('no_telp'),
                $this->input->post('alamat'),
                $this->input->post('jabatan_id'),
                $this->input->post('golongan_id'),
                $this->com_user['user_id'],
                $this->input->post('user_id')
            );
            //insert
            if ($this->m_operator->update_users($params)) {
                // upload image
                if (!empty($_FILES['user_img']['tmp_name'])) {
                    // unlink old file
                    $file_old = 'resource/doc/images/users/' . $this->input->post('user_img');
                    if (file_exists($file_old)) {
                        unlink($file_old);
                    }
                    $ext = pathinfo($_FILES['user_img']['name'], PATHINFO_EXTENSION);
                    // mengganti filename dengan user_id + extensi
                    $filename = $this->input->post('user_id') . "." . $ext;
                    // load
                    $this->load->library('tupload');
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
                        $this->m_operator->update_nama_file(array($filename, $this->input->post('user_id')));
                        //$this->m_operator->update_icon(array($data['file_name'], $image));
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    } else {
                        // default error
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                        $this->tnotification->sent_notification("error", "Data gagal disimpan");
                    }
                }
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/pegawai/edit/" . $this->input->post('user_id', 0));
    }

    //
    //-----------PERMISSIONS----------
    //
    public function permission($id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/operator_permissions/edit.html");
        // get user permissions
        $result = $this->m_operator->get_permission_by_id($id);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("user_id", $id);
        // get role name
        $this->smarty->assign("role_list", $this->m_operator->get_all_role());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses tanggal
    public function permission_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('user_id', 'ID User');
        $this->tnotification->set_rules('role_id[]', 'ID Role');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $user_id = $this->input->post('user_id');
            $role_id = $this->input->post('role_id');
            // delete role
            $this->m_operator->delete_role_by_user_id($user_id);
            if (!empty($role_id)) {
                foreach ($role_id as $value) {
                    if (!$this->m_operator->is_exist_role(array($user_id, $value))) {
                        $this->m_operator->insert_com_role_user(array($value, $user_id));
                    }
                }
            } else {
                $this->m_operator->delete_role_by_user_id($user_id);
            }
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/operator/permission/" . $this->input->post('user_id'));
    }

    // 
    //-----------ACCOUNT SETTING------------
    //
    public function account($id = "") {

        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/operator_account/edit.html");
        // get data account
        $result = $this->m_operator->get_user_account($id);
        //        echo '<pre>';print_r($result);exit();
        // password
        
        $result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("user_id", $id);
        // get role name
           
        $this->smarty->assign("selected", $this->m_operator->get_all_role_chk($id));
        
        $this->smarty->assign("rs_role", $this->m_operator->get_all_role());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function edit_account_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('user_id', 'User ID', 'trim|required|max_length[11]');
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_name_old', 'Username Lama', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[255]');
        // cek role
        $this->tnotification->set_rules('role_id[]', 'ID Role', 'trim|required');
        // check username
        $username = $this->input->post('user_name');
        $username_old = $this->input->post('user_name_old');
        if ($username <> $username_old) {
            if ($this->m_operator->is_exist_username($username)) {
                $this->tnotification->set_error_message("Username not available!");
            }
        }
        // cek user
        if ($this->input->post('user_id') == $this->com_user['user_id']) {
            $this->tnotification->set_error_message("Tidak bisa mengganti permission user sendiri");
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('user_name'),
                $this->input->post('user_pass'),
                $this->input->post('user_id')
            );
            // update
            if ($this->m_operator->update_account($params)) {
                $user_id = $this->input->post('user_id');
                $role_id = $this->input->post('role_id');
                $this->m_operator->delete_role_by_user_id($user_id);
                // delete role user
                foreach ($role_id as $value) {
                    
                    $this->m_operator->insert_com_role_user(array($value, $user_id));
                }
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
        redirect("pengaturan/operator/account/" . $this->input->post('user_id'));
    }

    //hapus
    public function delete($id = "") {
        //set rule
        $this->_set_page_rule("D");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/pegawai/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_operator->get_users_by_id($id));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    //proses hapus
    public function delete_process() {
        //set rule
        $this->_set_page_rule("D");
        //cek input
        $this->tnotification->set_rules('user_id', 'trim|required');
        // validasi delete user self
        if ($this->input->post('user_id') == $this->com_user['user_id']) {
            $this->tnotification->set_error_message("Tidak bisa mengapus user sendiri");
        }
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('user_id'));
            if ($this->m_operator->delete($params)) {
                $this->m_operator->delete_users($params);
                //$this->m_operator->delete_school($params);
                //$this->m_operator->delete_college($params);
                //$this->m_operator->delete_training($params);
                //$this->m_operator->delete_work($params);
                $this->m_operator->delete_role($params);
                // unlink file users
                $file_old = 'resource/doc/images/users/' . $this->input->post('user_img');
                if (file_exists($file_old)) {
                    unlink($file_old);
                }
                // end unlink file users
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
                redirect("pengaturan/pegawai");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        //default redirect
        redirect("pengaturan/pegawai/delete/");
    }

}
