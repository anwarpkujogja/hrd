<?php

class m_dashboard extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    /*
     * SURAT MASUK
     */

    // get user detail
    function get_str_6month($params)
    {
        $sql = "SELECT a.*,b.FS_NM_PEG
                FROM HOSPITAL.dbo.TD_PEG2 a
                LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
                WHERE FD_TGL_EXP_REGISTER BETWEEN ? AND ?
                AND b.FB_AKTIF_DINAS = 1
                ORDER BY FD_TGL_EXP_REGISTER ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        }
        return array();
    }
    function get_sip_6month($params)
    {
        $sql = "SELECT *
                FROM HOSPITAL.dbo.TD_PEG
                WHERE FD_TGL_AKHIR_IJIN_PRAKTEK BETWEEN ? AND ?
                AND FB_AKTIF_DINAS = 1
                ORDER BY FD_TGL_AKHIR_IJIN_PRAKTEK ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        }
        return array();
    }

    function get_sik_6month($params)
    {
        $sql = "SELECT *
                FROM HOSPITAL.dbo.TD_PEG
                WHERE FD_TGL_AKHIR_IJIN_KERJA BETWEEN ? AND ?
                AND FB_AKTIF_DINAS = 1
                ORDER BY FD_TGL_AKHIR_IJIN_KERJA ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        }
        return array();
    }

    function get_jenis_peg()
    {
        $sql = "SELECT b.FS_NM_JENIS_PEG,COUNT(a.FS_KD_PEG)'TOTAL'
		FROM HOSPITAL.dbo.TD_PEG a
		INNER JOIN HOSPITAL.dbo.TD_JENIS_PEG b ON a.FS_KD_JENIS_PEG=b.FS_KD_JENIS_PEG
		WHERE a.FB_AKTIF_DINAS = '1'
		GROUP BY b.FS_NM_JENIS_PEG";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    function get_total_peg()
    {
        $sql = "SELECT COUNT(a.FS_KD_PEG)'TOTAL'
		FROM HOSPITAL.dbo.TD_PEG a
		INNER JOIN HOSPITAL.dbo.TD_JENIS_PEG b ON a.FS_KD_JENIS_PEG=b.FS_KD_JENIS_PEG
		WHERE a.FB_AKTIF_DINAS = '1'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
}
