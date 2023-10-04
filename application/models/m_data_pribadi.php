<?php

class m_data_pribadi extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        // load encrypt
        $this->load->library('encrypt');
    }

    function get_last_inserted_id()
    {
        return $this->db->insert_id();
    }

    function get_data_peg($params)
    {
        $sql = "SELECT *,HOSPITAL.dbo.if_get_umur(FD_TGL_DINAS,?) AS MasaKerja 
                FROM HOSPITAL.dbo.TD_PEG a
                WHERE a.FS_KD_PEG = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_data_peg_asuransi_bpjs($params)
    {
        $sql = "SELECT aa.fs_kd_komponen_imbalan, bb.fs_nm_komponen_imbalan, aa.fs_no_referensi, aa.fn_saldo_awal  
                from HOSPITAL.dbo.td_komponen_imbalan_peg aa 
                left join HOSPITAL.dbo.td_komponen_imbalan bb on aa.fs_kd_komponen_imbalan= bb.fs_kd_komponen_imbalan 
                where fb_asuransi=1 and aa.fs_kd_peg=? AND aa.fs_kd_komponen_imbalan='ASR0000301'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_data_peg_asuransi_jamsos($params)
    {
        $sql = "SELECT aa.fs_kd_komponen_imbalan, bb.fs_nm_komponen_imbalan, aa.fs_no_referensi, aa.fn_saldo_awal  
                from HOSPITAL.dbo.td_komponen_imbalan_peg aa 
                left join HOSPITAL.dbo.td_komponen_imbalan bb on aa.fs_kd_komponen_imbalan= bb.fs_kd_komponen_imbalan 
                where fb_asuransi=1 and aa.fs_kd_peg=? AND aa.fs_kd_komponen_imbalan='ASR0000101'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function update_pegawai($params)
    {
        $sql = "UPDATE HOSPITAL.dbo.TD_PEG SET FS_NM_ALIAS=?,FS_NM_PEG=?,FS_KD_NIP=?,
                FB_JNS_KELAMIN = ?,FS_TEMP_LAHIR=?,FD_TGL_LAHIR=?,FS_ALM_PEG=?,FS_ALM_TINGGAL_PEG=?,
                FS_HP_PEG=?,FS_TLP_PEG=?,FS_EMAIL=?
                WHERE FS_KD_PEG=?";
        return $this->db->query($sql, $params);
    }
}
