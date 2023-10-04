<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class permissions extends ApplicationBase {

    //contructor
    public function __construct() {
        //parent contructor
        parent::__construct();
        //load model
        $this->load->model('m_permissions');
        $this->smarty->assign('m_permissions', $this->m_permissions);
        //load library
        $this->load->library('encrypt');
        $this->load->library('pagination');
        $this->load->library('tnotification');
        // global assign
        $this->smarty->assign("encrypt", $this->encrypt);
    }

    //list view
    public function index() {
        //set rule
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/permissions/list.html");
        // pagination
        $config['base_url'] = site_url("pengaturan/permissions/index/");
        $config['total_rows'] = $this->m_permissions->get_total_permission();
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
        $this->smarty->assign("rs_permissions", $this->m_permissions->get_all_permission_limit($params));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    public function edit($id = '') {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/permissions/edit.html");
        // get role name
        $this->smarty->assign("role_list", $this->m_permissions->get_all_role());
        // get list
        $this->smarty->assign("result", $this->m_permissions->get_permission_by_id($id));
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        //set rule
        $this->_set_page_rule("U");
        //cek input
        $this->tnotification->set_rules('role_id', 'Role', 'trim|required');
        $this->tnotification->set_rules('user_id', 'Role', 'trim|required');
        //proses
        if ($this->input->post('user_id') == $this->com_user['user_id']) {
            $this->tnotification->set_error_message("Tidak bisa mengganti permission user sendiri");
        }
        if ($this->tnotification->run() !== FALSE) {
            $user_id = $this->input->post('user_id');
            $role_id = $this->input->post('role_id');
            // delete role user
            $this->m_permissions->delete_role_by_user_id($user_id);
            if (!empty($role_id)) {
                $this->m_permissions->insert_role_user(array($role_id, $user_id));
            } else {
                $this->m_permissions->update_role($role_id, $user_id,$user_id);
            }

            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/permissions/edit/" . $this->input->post('user_id'));
    }

}