<?php

class m_operator extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // get total users
    function get_total_users() {
        $sql = "SELECT COUNT(*)'total' FROM (
		SELECT a.user_id
                FROM PKU.dbo.tad_com_user a
                INNER JOIN  users b ON a.user_id = b.user_id
                INNER JOIN `PKU.dbo.tad_com_role_user` c ON a.`user_id` = c.`user_id`
                INNER JOIN `PKU.dbo.tad_com_role` d ON c.`role_id` = d.`role_id`
                WHERE a.user_id <> 1 AND d.`portal_id` = 2
                GROUP BY a.`user_id`
                ) res";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }
    // get total users
    function get_total_users_pegawai() {
        $sql = "SELECT COUNT(*)'total' FROM (
		SELECT a.user_id
                FROM PKU.dbo.tad_com_user a
                INNER JOIN  users b ON a.user_id = b.user_id
                WHERE a.user_id <> 1 AND a.user_name =''
                GROUP BY a.`user_id`
                ) res";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }

    // get total users
    function get_total_users_ph($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM PKU.dbo.tad_com_user a
                INNER JOIN  users b ON a.user_id = b.user_id
                INNER JOIN `PKU.dbo.tad_com_role_user` c ON a.`user_id` = c.`user_id`
                INNER JOIN `PKU.dbo.tad_com_role` d ON c.`role_id` = d.`role_id`
                WHERE nama_lengkap LIKE ? AND a.user_id <> 1 AND d.`portal_id` = 2 ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }

    // get all users
    function get_all_users() {
        $sql = "SELECT a.*, b.*,d.*, e.role_nm
                FROM PKU.dbo.tad_com_user a
                LEFT JOIN PKU.dbo.tad_com_role_user d ON a.user_id = d.user_id
                INNER JOIN PKU.dbo.tad_com_role e ON d.role_id = e.role_id
                JOIN HOSPITAL.dbo.TD_PEG b ON a.user_name = b.FS_KD_PEG
                WHERE a.user_id <> 1 AND e.portal_id = 2";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all users limit
    function get_all_users_limit($params) {
        $sql = "SELECT FS_KD_PEG,FS_NM_PEG,role_nm,a.user_id,FS_HP_PEG,FS_EMAIL
                FROM PKU.dbo.tad_com_user a
                INNER JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
                INNER JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
                INNER JOIN HOSPITAL.dbo.TD_PEG d ON a.user_name = d.FS_KD_PEG
                WHERE a.user_id <> 1 AND c.portal_id = 2 AND FB_AKTIF_DINAS = 1
                ORDER BY user_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    function get_all_users_limit_pegawai($params) {
        $sql = "SELECT a.user_id, a.user_mail, b.*, c.jabatan_nama
                FROM PKU.dbo.tad_com_user a
                LEFT JOIN users b ON a.user_id = b.user_id
                LEFT JOIN jabatan c ON b.jabatan_id = c.jabatan_id
                WHERE a.user_id <> 1 AND a.user_name=''
                ORDER BY b.nama_lengkap ASC 
                LIMIT ?, ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all users limit
    function get_all_users_limit_ph($params) {
        $sql = "SELECT a.user_id, a.user_mail, b.*, c.jabatan_nama, e.role_nm,d.role_id
                FROM PKU.dbo.tad_com_user a
                LEFT JOIN users b ON a.user_id = b.user_id
                LEFT JOIN jabatan c ON b.jabatan_id = c.jabatan_id
                INNER JOIN PKU.dbo.tad_com_role_user d ON a.`user_id` = d.`user_id`
                INNER JOIN PKU.dbo.tad_com_role e ON d.`role_id` = e.`role_id`
                WHERE a.user_id <> 1 AND e.`portal_id` = 2 AND nama_lengkap LIKE ?
                ORDER BY b.nama_lengkap ASC 
                LIMIT ?, ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get data jabatan
    function get_all_jabatan() {
        $sql = "SELECT * FROM jabatan";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    // get data jabatan
    function get_user_id($params) {
        $sql = "SELECT * FROM users a
                LEFT JOIN PKU.dbo.tad_com_role_user b ON a.`user_id`=b.`user_id`
                WHERE role_id=? AND a.user_id!=?";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get data golongan
    function get_all_golongan() {
        $sql = "SELECT * FROM golongan";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert users
    function insert_users($params) {
        $sql = "INSERT INTO users (user_id, unit_id, nip, nama_lengkap, jenis_kelamin, tmp_lahir, tgl_lahir, no_telp, alamat, jabatan_id, golongan_id, mdb, mdd)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
        return $this->db->query($sql, $params);
    }

    // update users
    function update_users($params) {
        $sql = "UPDATE users SET unit_id = ?, nip = ?, nama_lengkap = ?,jenis_kelamin = ?,tmp_lahir = ?, tgl_lahir = ?, 
                no_telp = ?, alamat = ?, jabatan_id = ?, golongan_id = ?, mdb = ?, mdd = NOW() 
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    //update email users
    function update_email_users($params) {
        $sql = "UPDATE PKU.dbo.tad_com_user SET user_mail = ?, mdb = ?, mdd = NOW() 
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // update users
    function update_user_detail($params) {
        $sql = "UPDATE users SET nama_lengkap = ?, alamat = ?, no_telp = ?, mdb = ?, mdd = NOW() 
            WHERE user_id = ? ";
        return $this->db->query($sql, $params);
    }

    // update nama file gambar
    function update_nama_file($params) {
        $sql = "UPDATE users SET user_img = ? 
            WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete users
    function delete_users($params) {
        $sql = "DELETE FROM users WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // get user by id
    function get_users_by_id($id) {
        $sql = "SELECT a.*, b.*, a.user_id, c.role_id, d.role_nm 
                FROM PKU.dbo.tad_com_user a
                LEFT JOIN PKU.dbo.tad_com_role_user c ON a.user_id = c.user_id
                LEFT JOIN PKU.dbo.tad_com_role d ON c.role_id = d.role_id
                LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.user_name = b.FS_KD_PEG 
                WHERE a.user_id = ?";
        $query = $this->db->query($sql, $id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get unit
    function get_list_unit() {
        $sql = "SELECT * FROM unit_kerja";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total user
    function get_total_operator($param) {
        $sql = "SELECT COUNT(*)'total' 
            FROM PKU.dbo.tad_com_user a 
            JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id 
            LEFT JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id 
            WHERE b.role_id<>1 AND user_name LIKE ? AND operator_name LIKE ? AND b.role_id LIKE ?";
        $query = $this->db->query($sql, $param);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get all user
    function get_all_operator($params) {
        $sql = "SELECT a.*, b.role_id, c.role_nm
            FROM PKU.dbo.tad_com_user a 
            JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
            JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
            WHERE b.role_id<>1 AND a.user_name LIKE ? AND a.operator_name LIKE ? AND b.role_id LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all user limit
    function get_all_operator_limit($params) {
        $sql = "SELECT a.*, b.role_id, c.role_nm
            FROM PKU.dbo.tad_com_user a 
            JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
            JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
            WHERE b.role_id<>1 AND a.user_name LIKE ? AND a.operator_name LIKE ? AND b.role_id LIKE ?
            ORDER BY a.user_name ASC LIMIT ?, ?";
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
    function get_detail_operator_by_id($params) {
        $sql = "SELECT a.*, b.role_id, c.role_nm, c.default_page
                FROM PKU.dbo.tad_com_user a
                LEFT JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id
                LEFT JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
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

    // get detail user by id
    function get_detail_users_by_id($params) {
        $sql = "SELECT * from users WHERE user_id = ?";
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
    function is_exist_username($params) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_user WHERE user_name = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }

    // check password
    function is_exist_password($user_id, $password) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $user_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // --
        $password_decode = $this->encrypt->decode($result['user_pass'], $result['user_key']);
        if ($password_decode == $password) {
            return true;
        } else {
            return false;
        }
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

    // update PKU.dbo.tad_com_user
    function update_operator($params) {
        $sql = "UPDATE PKU.dbo.tad_com_user SET user_name = ?, user_pass = ?, user_key = ?, 
                user_mail = ? , mdb = ?, mdd = NOW()
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert operator
    function insert($params) {

        $sql = "INSERT INTO PKU.dbo.tad_com_user (user_name, user_pass, user_key, user_mail, lock_st, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
        return $this->db->query($sql, $params);
    }

    // insert role
    function insert_role($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_role_user (role_id, user_id)
                VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    // get all user limit
    function get_operator_by_idd($params) {
        $sql = "SELECT* FROM(
                    SELECT a.*, b.role_id, c.role_nm 
                    FROM PKU.dbo.tad_com_user a 
                    JOIN PKU.dbo.tad_com_role_user b ON a.user_id = b.user_id 
                    RIGHT JOIN PKU.dbo.tad_com_role c ON b.role_id = c.role_id
                )r1
                WHERE r1.role_id <> 1 AND r1.user_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update operator
    function update($params) {
        $sql = "UPDATE PKU.dbo.tad_com_user SET user_name=?,user_pass=?,user_key=?,lock_st=?,user_mail=?,operator_name=?,
                operator_phone=?,operator_hp=?,operator_notes=?,mdb=?, mdd=NOW() WHERE user_id=? ";
        return $this->db->query($sql, $params);
    }

    // update role
    function update_role($params) {
        $sql = "UPDATE PKU.dbo.tad_com_role_user SET role_id = ?, user_id = ? WHERE user_id = ? ";
        return $this->db->query($sql, $params);
    }

    // delete
    function delete($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_user WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete  role
    function delete_role($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_role_user WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    //----------PERMISSIONS---------------
    // get role bukan superadmin
    function get_all_role() {
        $sql = "SELECT * FROM PKU.dbo.tad_com_role WHERE portal_id =2 ORDER BY role_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get role bukan superadmin
    function get_all_role_ph() {
        $sql = "SELECT * FROM PKU.dbo.tad_com_role WHERE portal_id =2 AND role_id=13 ORDER BY role_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_all_role_chk($params) {
        $sql = "SELECT a.`role_id`,a.`role_nm`
                FROM PKU.dbo.tad_com_role a
                LEFT JOIN PKU.dbo.tad_com_role_user b ON a.role_id=b.role_id
                LEFT JOIN users c ON b.user_id=c.user_id
                WHERE c.user_id=? AND a.portal_id =2";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    function get_all_role_unchk($params) {
        $sql = "SELECT a.`role_id`,a.`role_nm`
                FROM PKU.dbo.tad_com_role a
                WHERE a.role_id != ? AND a.portal_id =2
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get check role
    function check_is_selected($params) {
        $sql = "SELECT COUNT(*) AS total
            FROM PKU.dbo.tad_com_role_user
            WHERE role_id = ? AND user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get permission by id
    function get_permission_by_id($id) {
        $sql = "SELECT a.*, b.*, d.role_nm, d.role_id 
            FROM users a
            LEFT JOIN PKU.dbo.tad_com_user b ON a.user_id = b.user_id
            LEFT JOIN PKU.dbo.tad_com_role_user c ON b.user_id = c.user_id 
            LEFT JOIN PKU.dbo.tad_com_role d ON c.role_id = d.role_id
            WHERE a.user_id = ?
            ORDER BY a.user_id ASC";
        $query = $this->db->query($sql, $id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // delete role by user_id
    function delete_role_by_user_id($params) {
        $sql = "DELETE FROM PKU.dbo.tad_com_role_user WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // is role already exist
    function is_exist_role($params) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_role_user WHERE user_id = ? AND role_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }

    // insert role
    function insert_com_role_user($params) {
        $sql = "INSERT INTO PKU.dbo.tad_com_role_user (role_id, user_id)
            VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    //----------ACCOUNT---------------
    // get user account
    function get_user_account($id) {
        $sql = "SELECT a.*, b.*, c.role_id, d.role_nm 
            FROM PKU.dbo.tad_com_user a
	    LEFT JOIN PKU.dbo.tad_com_role_user c ON a.user_id = c.user_id
	    LEFT JOIN PKU.dbo.tad_com_role d ON c.role_id = d.role_id
            LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.user_name = b.FS_KD_PEG
            WHERE a.user_id = ?";
        $query = $this->db->query($sql, $id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function check_is_selected_for($id) {
        $sql = "SELECT count(*) FROM PKU.dbo.tad_com_user a
	    LEFT JOIN PKU.dbo.tad_com_role_user c ON a.`user_id` = c.`user_id`
	    LEFT JOIN PKU.dbo.tad_com_role d ON c.`role_id` = d.`role_id`
            LEFT JOIN users b ON a.user_id = b.user_id
            WHERE b.user_id = ?";
        $query = $this->db->query($sql, $id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update account
    function update_account($params) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $params[2]);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // encode password
        $params[1] = $this->encrypt->encode($params[1], $result['user_key']);
        // update 
        $sql = "UPDATE PKU.dbo.tad_com_user SET user_name = ?, user_pass = ? WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // update account
    function update_account_ph($params) {
        $sql = "SELECT * FROM PKU.dbo.tad_com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $params[3]);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // encode password
        $params[1] = $this->encrypt->encode($params[1], $result['user_key']);
        // update 
        $sql = "UPDATE PKU.dbo.tad_com_user SET user_name = ?, user_pass = ?, lock_st=? WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

}
