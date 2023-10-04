<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class operator_upt extends ApplicationBase {

    //contructor
    public function __construct() {
        //parent contructor
        parent::__construct();
        //load model
        $this->load->model('m_operator_upt');
        $this->smarty->assign("m_operator_upt", $this->m_operator_upt);
        //load library
        $this->load->library('encrypt');
        $this->load->library('pagination');
        $this->load->library('tnotification');
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-timepicker-addon.js');
        $this->smarty->load_javascript('resource/js/jquery/select2.js');
        // load style
        $this->smarty->load_style("jquery.ui/select2/select2.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
    }

    //list view
    public function index() {
        //set rule
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/operator_upt/list.html");
        // pagination
        $config['base_url'] = site_url("pengaturan/operator_upt/index/");
        $config['total_rows'] = $this->m_operator_upt->get_total_users();
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
        $this->smarty->assign("rs_id", $this->m_operator_upt->get_all_users_limit($params));
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
        $this->smarty->assign("template_content", "pengaturan/operator_upt/add.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        // get list
        $img_path = 'resource/doc/images/users/default.png';
        $this->smarty->assign("image_path", base_url() . $img_path);
        // bandar udara
        $this->smarty->assign("instansi", $this->m_operator_upt->get_list_instansi());
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
        // com_user
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required');
        $this->tnotification->set_rules('user_instansi', 'Instansi', 'trim|required');
        // check username
        $username = trim($this->input->post('user_name'));
        if ($this->m_operator_upt->is_exist_username($username)) {
            $this->tnotification->set_error_message('Username is not available');
        }
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $password_key = crc32($this->input->post('user_pass'));
            $password = $this->encrypt->encode($this->input->post('user_pass'), $password_key);
            $params = array(
                $this->input->post('user_name'),
                $password,
                $password_key,
                $this->input->post('user_mail'),
                '0',
                $this->com_user['user_id']
            );
            //insert
            if ($this->m_operator_upt->insert($params)) {
                $user_id = $this->m_operator_upt->get_last_inserted_id();
                $params = array(
                    $user_id,
                    $this->input->post('user_instansi'),
                    $this->com_user['user_id']
                );
                $this->m_operator_upt->insert_users($params);
                // process role bandara wilayah kerja
                $role_id = 11;
                $params = array($role_id, $user_id);
                $this->m_operator_upt->insert_com_role_user(array($role_id, $user_id));
                // upload image
                if (!empty($_FILES['user_img']['tmp_name'])) {
                    $ext = pathinfo($_FILES['user_img']['name'], PATHINFO_EXTENSION);
                    // mengganti filename dengan user_id + extensi
                    $filename = $user_id . "." . $ext;
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
                        $this->m_operator_upt->update_nama_file(array($filename, $user_id));


                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    } else {
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                        $this->tnotification->sent_notification("error", "Data gagal disimpan");
                    }
                }
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
        redirect("pengaturan/operator_upt/add");
    }

    public function edit($id = '') {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/operator_upt/edit.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        // get list
        $result = $this->m_operator_upt->get_users_by_id($id);
        $result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
        // folder image img
        $img_path = 'resource/doc/images/users/' . $result['user_img'];
        // jika tidak ditemukan file, pakai image default
        if (!is_file($img_path)) {
            $img_path = 'resource/doc/images/users/default.png';
        }
        if (!empty($result)) {
            $this->smarty->assign("result", $result);
            $this->smarty->assign("user_name", $result['user_name']);
        }
        // images
        $this->smarty->assign("image_path", base_url() . $img_path);
        // bandar udara
        $this->smarty->assign("instansi", $this->m_operator_upt->get_list_instansi());
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
        $this->tnotification->set_rules('user_id', 'User ID', 'trim');
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required');
        $this->tnotification->set_rules('user_instansi', 'Instansi', 'trim|required');

        // get data
        $data = $this->m_operator_upt->get_users_by_id($this->input->post('user_id'));
        if (!empty($data)) {
            $data['user_pass'] = $this->encrypt->decode($data['user_pass'], $data['user_key']);
        }
        // check username
        $username = trim($this->input->post('user_name'));
        $username_old = $data['user_name'];
        if ($username != $username_old) {
            if ($this->m_operator_upt->is_exist_username($username)) {
                $this->tnotification->set_error_message('Username is not available');
            }
        }
        //proses
        if ($this->tnotification->run() !== FALSE) {
            $password_key = crc32($this->input->post('user_pass'));
            $password = $this->encrypt->encode($this->input->post('user_pass'), $password_key);
            // update com_user
            $params = array(
                $this->input->post('user_name'),
                $password,
                $password_key,
                $this->input->post('user_mail'),
                $this->com_user['user_id'],
                $this->input->post('user_id')
            );
            $params_2 = array(
                $this->input->post('user_instansi'),
                $this->com_user['user_id'],
                $this->input->post('user_id')
            );
//            //insert
            if ($this->m_operator_upt->update_users($params)) {
                $this->m_operator_upt->update_user_detail($params_2);
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
                        $this->m_operator_upt->update_nama_file(array($filename, $this->input->post('user_id')));
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
        redirect("pengaturan/operator_upt/edit/" . $this->input->post('user_id', 0));
    }

    //hapus
    public function delete($user_id = "") {
        //set rule
        $this->_set_page_rule("D");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/operator_upt/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_operator_upt->get_users_by_id($user_id));
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
            if ($this->m_operator_upt->delete($params)) {
                $this->m_operator_upt->delete_users($params);
                $this->m_operator_upt->delete_role($params);
                // unlink file users
                $file_old = 'resource/doc/images/users/' . $this->input->post('user_img');
                if (file_exists($file_old)) {
                    unlink($file_old);
                }
                // end unlink file users
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                //default redirect
                redirect("pengaturan/operator_upt");
            } else {
                //default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        //default redirect
        redirect("pengaturan/operator_upt/");
    }

}
