<?php

class m_kepeg_gaji_berkala extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    /*
     * SURAT MASUK
     */

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

   function get_pangkat(){
      $sql = " SELECT aa.fs_kd_pangkat, aa.fs_nm_pangkat FROM HOSPITAL.dbo.td_pangkat aa ";
      $query = $this->db->query($sql, $params);
      if ($query->num_rows() > 0) {
          $result = $query->result_array();
          $query->free_result();
          return $result;
      } else {
          return array();
      }
   }

   function get_golongan(){
        $sql = "SELECT aa.fs_kd_golongan, aa.fs_nm_golongan FROM HOSPITAL.dbo.td_golongan aa";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_status(){
        $sql = "SELECT fs_nm_status_peg_rs, fs_kd_status_peg, fs_kd_status_peg_rs, fs_kd_tipe_jaminan,fs_kd_status_peg_tax 
                FROM HOSPITAL.dbo.td_status_peg_rs";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    function get_aktif(){
        $sql = "SELECT fb_aktif, fb_aktif_name
                FROM HOSPITAL.dbo.TD_FB_AKTIF";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }


   function get_history_peg($params){
        $sql = " SELECT TOP (3) aa.FS_KD_PEG, aa.FS_NO_SK, aa.FB_AKTIF,  aa.FS_KD_JENIS_KENAIKAN_GAJI, aa.FD_TGL_SK, aa.FS_NM_PEJABAT, aa.fs_kd_golongan, 
                aa.FN_GAJI_POKOK, aa.FN_TUNJANGAN1, aa.FN_TUNJANGAN2, aa.FN_TUNJANGAN3, aa.FN_TUNJANGAN4, aa.FN_TUNJANGAN5, aa.FB_AKTIF, aa.FN_TJ_PULSA,
                aa.FN_TUNJANGAN6, aa.FN_TUNJANGAN7,aa.FN_TUNJANGAN8, aa.FN_TUNJANGAN9, aa.FS_KD_TRS_KENAIKAN, 
                aa.fs_kd_jenis_kenaikan_gaji
                FROM HOSPITAL.dbo.td_rgaji_berkala_peg aa 
                WHERE aa.FS_KD_PEG = ? ORDER BY FN_GAJI_POKOK DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
   }

   
   function get_pegawai($params) {
        $sql = "SELECT TOP (1) a.*, b.*, k.FS_NO_SK, k.FD_TGL_SK, k.FS_NM_PEJABAT,
                k.FN_GAJI_POKOK, k.FN_TUNJANGAN1, k.FN_TUNJANGAN2, k.FN_TUNJANGAN3, k.FN_TUNJANGAN4, k.FN_TUNJANGAN5, k.FN_TUNJANGAN8, k.FN_TJ_PULSA, k. FB_AKTIF, k.FS_KD_TRS_KENAIKAN, k.FS_KD_JENIS_KENAIKAN_GAJI
                ,j.FD_TGL_KONTRAK, j.FD_TGL_KONTRAK_B, j.FS_MASA_KERJA,
                HOSPITAL.dbo.if_get_umur(FD_TGL_DINAS,?) AS MasaKerja
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN PKU.dbo.TAD_TRS_PAYROLL f ON a.FS_KD_PEG = f.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG k ON a.FS_KD_PEG = k.FS_KD_PEG 
                LEFT JOIN HOSPITAL.dbo.TD_RPANGKAT_PEG j ON a.FS_KD_PEG = j.FS_KD_PEG
                WHERE a.FS_KD_PEG = ? AND a.FB_AKTIF_DINAS = '1' ORDER BY FS_KD_TRS_KENAIKAN DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_edit_pegawai($params) {
        $sql = "SELECT a.*, b.*,j.FS_KD_TRS, k.FS_NO_SK, k.FD_TGL_SK, k.FS_NM_PEJABAT,
                k.FN_GAJI_POKOK, k. FB_AKTIF, k.FS_KD_TRS_KENAIKAN, k.FS_KD_JENIS_KENAIKAN_GAJI
                ,j.FD_TGL_KONTRAK, j.FD_TGL_KONTRAK_B, j.FS_MASA_KERJA, k.*,
                DATEDIFF(year, c.FD_TGL_TETAP, GETDATE()) AS MasaKerja
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN PKU.dbo.TAD_TRS_PAYROLL f ON a.FS_KD_PEG = f.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG k ON a.FS_KD_PEG = k.FS_KD_PEG 
                LEFT JOIN HOSPITAL.dbo.TD_RPANGKAT_PEG j ON a.FS_KD_PEG = j.FS_KD_PEG
                WHERE a.FS_KD_PEG = ? AND k.FS_KD_TRS_KENAIKAN = ? AND a.FB_AKTIF_DINAS = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_no_trs_kenaikan() {
        $sql = "SELECT MAX(FS_KD_TRS_KENAIKAN) AS KENAIKAN FROM HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }


    function insert_gaji_berkala($params){
        $sql = "INSERT INTO HOSPITAL.dbo.td_rgaji_berkala_peg (FS_KD_PEG, FS_NO_SK, FD_TGL_SK, FS_NM_PEJABAT, FN_GAJI_POKOK, FB_AKTIF, 
                            FN_TUNJANGAN1, FN_TUNJANGAN2, FN_TUNJANGAN3, FN_TUNJANGAN4, FN_TUNJANGAN5,
                            FN_TUNJANGAN6, FN_TUNJANGAN7, FN_TUNJANGAN8,
                            FS_KD_TRS_KENAIKAN, FS_KD_JENIS_KENAIKAN_GAJI, FS_KD_GOLONGAN,
                            FN_TUNJANGAN9, FN_TJ_PULSA) 
                            VALUES (? ,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->query($sql, $params);
    }

    function update_gaji_berkala($params) {
        $sql = "UPDATE HOSPITAL.dbo.td_rgaji_berkala_peg
                SET FS_NO_SK = ?,  FD_TGL_SK = ?, FS_NM_PEJABAT = ?, FN_GAJI_POKOK = ?, FB_AKTIF = ?, FN_TUNJANGAN1 = ?, FN_TUNJANGAN2=?,
                FN_TUNJANGAN3 = ?, FN_TUNJANGAN4 = ?, FN_TUNJANGAN5 = ?, FN_TUNJANGAN6 = ?, FN_TUNJANGAN7 = ?, FN_TUNJANGAN8 = ?,
                FS_KD_JENIS_KENAIKAN_GAJI = ?, FS_KD_GOLONGAN = ?, FN_TUNJANGAN9 = ?, FN_TJ_PULSA = ?
                WHERE FS_KD_TRS_KENAIKAN = ?";
        return $this->db->query($sql, $params);
    }


    function get_jenis_kenaikan_gaji(){
        $sql = " SELECT aa.fs_kd_jenis_kenaikan_gaji, aa.fs_nm_jenis_kenaikan_gaji 
                 FROM HOSPITAL.dbo.td_jenis_kenaikan_gaji aa";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /*function insert_gaji_berkalas(){
        $sql = "INSERT INTO HOSPITAL.dbo.td_rgaji_berkala_peg 
                VALUES (?, ?, ?, ?, ?, ?)";
       return $this->db->query($sql, $params);
    }*/


   
   
}
