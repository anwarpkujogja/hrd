<?php

class m_settings extends CI_Model {

    var $db;
    var $db_hos;

    public function __construct() {
        parent::__construct();
        $this->db_hos = $this->load->database('hospital', true);
        $this->db = $this->load->database('default', true);
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // <editor-fold defaultstate="collapsed" desc="PORTAL MANAGEMENT">
    // get total data
    function get_total_data_portal() {
        $sql = "SELECT COUNT(*)'total' FROM PKU.dbo.tad_com_portal";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get all portal
    function get_all_portal() {
        $sql = "SELECT * FROM PKU.dbo.tad_com_portal";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail data by id
    function get_portal_by_id($portal_id) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_portal WHERE portal_id = ?";
        $query = $this->db->query($sql, $portal_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert data portal
    function insert_portal($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_portal (portal_nm, site_title, site_desc, meta_desc, meta_keyword, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
        return $this->db->query($sql, $params);
    }

    // update data portal
    function update_portal($params) {
        $sql = "UPDATE PKU.dbo.tad_com_portal SET portal_nm = ?, site_title = ?, site_desc = ?, meta_desc = ?, meta_keyword = ?,
                mdb = ?, mdd = GETDATE()
                WHERE portal_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete data portal
    function delete_portal($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_portal WHERE portal_id = ?";
        return $this->db->query($sql, $params);
    }

    // </editor-fold>
    // ------------------
    // <editor-fold defaultstate="collapsed" desc="ROLE MANAGEMENT">
    // get all roles
    function get_all_roles() {
        $sql = "SELECT b.portal_nm, a.* 
                FROM PKU.dbo.tad_com_role a
                INNER JOIN PKU.dbo.tad_com_portal b ON a.portal_id = b.portal_id
                WHERE a.role_id <> 1
                ORDER BY b.portal_nm ASC, role_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail role by id
    function get_detail_role_by_id($id_role) {
        $sql = "SELECT a.*, b.portal_nm
                FROM PKU.dbo.tad_com_role a
                INNER JOIN PKU.dbo.tad_com_portal b ON a.portal_id = b.portal_id
                WHERE role_id = ?";
        $query = $this->db->query($sql, $id_role);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert role
    function insert_role($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_role (portal_id, role_nm, role_desc, default_page)
                VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, $params);
    }

    // update role
    function update_role($params) {
        $sql = "UPDATE PKU.dbo.tad_com_role SET portal_id = ?, role_nm = ?, role_desc = ?, default_page = ?
                WHERE role_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete role
    function delete_role($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_role WHERE role_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert role menu
    function insert_role_menu($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_role_menu (role_id, nav_id, role_tp) VALUES (?, ?, ?)";
        return $this->db->query($sql, $params);
    }

    // delete role menu
    function delete_role_menu($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_role_menu WHERE role_id = ?";
        return $this->db->query($sql, $params);
    }

    // </editor-fold>
    // ------------------
    // <editor-fold defaultstate="collapsed" desc="MENU MANAGEMENT">
    // get all portal with menu
    function get_all_portal_menu() {
        $sql = "SELECT a.portal_id,a.portal_nm,a.site_title, COUNT(b.nav_id)'total_menu' 
                FROM PKU.dbo.tad_com_portal a
                LEFT JOIN PKU.dbo.tad_com_menu b ON a.portal_id = b.portal_id
                GROUP BY a.portal_id,a.portal_nm,a.site_title
                ORDER BY portal_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all menu by parent
    function get_all_menu_by_parent($params) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_menu
                WHERE portal_id = ? AND parent_id = ? ORDER BY nav_no ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all menu by parent
    function get_all_menu_selected_by_parent($params) {
        $sql = "SELECT a.*, b.role_id, b.role_tp
                FROM PKU.dbo.tad_com_menu a
                LEFT JOIN (SELECT * FROM PKU.dbo.tad_com_role_menu WHERE role_id = ?) b ON a.nav_id = b.nav_id
                WHERE portal_id = ? AND parent_id = ?
                ORDER BY nav_no ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail menu by id
    function get_detail_menu_by_id($id_role) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_menu WHERE nav_id = ?";
        $query = $this->db->query($sql, $id_role);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert menu
    function insert_menu($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_menu (portal_id, parent_id, nav_title, nav_desc, nav_url, nav_no, active_st, display_st, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";
        return $this->db->query($sql, $params);
    }

    // update menu
    function update_menu($params) {
        $sql = "UPDATE PKU.dbo.tad_com_menu
                SET portal_id = ?, parent_id = ?, nav_title = ?, nav_desc = ?, nav_url = ?, nav_no = ?, active_st = ?, display_st = ?, mdb = ?
                WHERE nav_id = ?";
        return $this->db->query($sql, $params);
    }

    // update icon
    function update_icon($params) {
        $sql = "UPDATE PKU.dbo.tad_com_menu SET nav_icon = ? WHERE nav_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete menu
    function delete_menu($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_menu WHERE nav_id = ?";
        return $this->db->query($sql, $params);
    }

    // update parent
    function update_parent($params) {
        $sql = "UPDATE PKU.dbo.tad_com_menu SET parent_id = ? WHERE parent_id = ?";
        return $this->db->query($sql, $params);
    }

    // </editor-fold>
    // ------------------
    // <editor-fold defaultstate="collapsed" desc="USER DEFAULT MANAGEMENT">
    // get total user
    function get_total_users() {
        $sql = "SELECT COUNT(*) AS 'total'
                FROM PKU.dbo.tad_com_user a
                INNER JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
                INNER JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get all user limit
    function get_all_users($params) {
        $sql = "SELECT *
                FROM PKU.dbo.tad_com_user a
                INNER JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
                INNER JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
                INNER JOIN HOSPITAL.dbo.TD_PEG d ON a.user_name = d.FS_KD_PEG";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail user by id
    function get_detail_user_by_id($params) {
        $sql = "SELECT *
                FROM PKU.dbo.tad_com_user a
                INNER JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
                INNER JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
                INNER JOIN HOSPITAL.dbo.TD_PEG d ON a.user_name = d.FS_KD_PEG
                WHERE a.user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // check username
    function is_exist_username($username) {
        $sql = "SELECT COUNT(*)'total' FROM PKU.dbo.tad_com_user WHERE user_name = ?";
        $query = $this->db->query($sql, $username);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            if ($result['total'] == 0) {
                return false;
            }
        }
        return true;
    }

    // check email
    function is_exist_email($email) {
        $sql = "SELECT COUNT(*)'total' FROM PKU.dbo.tad_com_user WHERE user_mail = ?";
        $query = $this->db->query($sql, $email);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            if ($result['total'] == 0) {
                return false;
            }
        }
        return true;
    }

    // insert user
    function insert_user($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_user (user_name, user_pass, user_key, lock_st, mdb, mdd) VALUES ( ?, ?, ?, ?, ?, GETDATE())";
        return $this->db->query($sql, $params);
    }

    // insert user detail
    function insert_user_detail($params) {
        $sql = "INSERT INTO users (user_id, nama_lengkap, alamat, no_telp, mdb,mdd) VALUES (?, ?, ?, ?, ?, GETDATE())";
        return $this->db->query($sql, $params);
    }

    // update user
    function update_user($params) {
        $sql = "UPDATE PKU.dbo.tad_com_user SET user_name = ?, user_pass = ?, user_key = ?, lock_st = ?, user_mail = ?,
                mdb = ?, mdd = GETDATE() 
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // update user detail
    function update_user_detail($params) {
        $sql = "UPDATE users SET nama_lengkap = ?, alamat = ?, no_telp = ?, mdb = ?, mdd = GETDATE() 
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete user
    function delete_user($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_user WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert role user
    function insert_role_user($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_role_user (role_id, user_id)                 VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    // update role user
    function update_role_user($params) {
        $sql = "UPDATE PKU.dbo.tad_com_role_user SET role_id = ? WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete role user
    function delete_role_user($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_role_user WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // </editor-fold>
}