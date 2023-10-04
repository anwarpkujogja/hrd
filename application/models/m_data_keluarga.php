<?php

class m_data_keluarga extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }
    
    //insert data
    function insert($params) {
        $sql = "INSERT INTO TAD_TRS_FILE_PEG ( FS_KD_PEG, FS_NM_FILE_PEG_KET, mdb, mdd)
            VALUES ( ?, ?, ?, GETDATE())";
        return $this->db->query($sql, $params);
    }

    //update data
    function update_att($params) {
        $sql = "UPDATE TAD_TRS_FILE_PEG 
            SET  FS_NM_FILE_PEG = ? WHERE FS_KD_FILE_PEG = ?";
        return $this->db->query($sql, $params);
    }

    //delete data
    function delete($params) {
        $sql = "DELETE FROM TAD_TRS_FILE_PEG WHERE FS_KD_FILE_PEG = ?";
        return $this->db->query($sql, $params);
    }

    //get all data
    function get_all_keluarga($params) {
        $sql = "SELECT * FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_PEG LIKE ?
                ORDER BY FS_KD_PEG ASC";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    function get_all_att_by_id($params) {
        $sql = "SELECT * FROM TAD_TRS_FILE_PEG
                WHERE FS_KD_PEG = ?
                ORDER BY mdd ASC";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get by id for searching
    function get_all_keluarga_by_id($id) {
        $sql = "SELECT * FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_PEG = ?";
        $query = $this->db->query($sql, $id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
