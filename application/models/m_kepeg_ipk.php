<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_kepeg_ipk extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    //get all data
    function get_data_peg_by_search($params) {
        $sql = "SELECT FS_NM_PEG,a.FS_KD_PEG,FS_NM_BAGIAN,FS_KD_GOLONGAN,FS_REK_BANK,
                DATEDIFF(year, c.FD_TGL_DINAS, GETDATE()) AS MasaKerja,FS_NM_PENDIDIKAN,
                d.FS_KD_TINGKAT_IJAZAH,FS_NM_JABATAN,FS_KD_JENIS_JABATAN,FS_NM_GRUP_BAGIAN,
                h.FS_KD_GRUP_BAGIAN
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_RPENDIDIKAN_PEG d ON a.FS_KD_PEG=d.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_PENDIDIKAN e ON d.FS_KD_TINGKAT_IJAZAH=e.FS_KD_PENDIDIKAN
                LEFT JOIN HOSPITAL.dbo.TD_RJABATAN_PEG f ON a.FS_KD_PEG=f.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_JABATAN g ON g.FS_KD_JABATAN=f.FS_KD_JABATAN
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                WHERE a.FS_KD_PEG = ? AND d.FB_AKTIF='True' AND f.FB_AKTIF = 'True'";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    function get_data_peg_by_search_trs($params) {
        $sql = "SELECT FS_NM_PEG,a.FS_KD_PEG,FS_NM_BAGIAN,FS_KD_GOLONGAN,FS_REK_BANK,
                DATEDIFF(year, FD_TGL_DINAS, GETDATE()) AS MasaKerja,
                FS_NM_GRUP_BAGIAN,
                h.FS_KD_GRUP_BAGIAN,i.*
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN TAD_IPK i ON a.FS_KD_PEG=i.FS_KD_PEG
                WHERE i.FS_KD_TRS = ?";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    /*function get_data_peg_by_search_trs($params) {
        $sql = "SELECT FS_NM_PEG,a.FS_KD_PEG,FS_NM_BAGIAN,FS_KD_GOLONGAN,FS_REK_BANK,
                DATEDIFF(year, c.FD_TGL_KONTRAK_I, GETDATE()) AS MasaKerja,FS_NM_PENDIDIKAN,
                d.FS_KD_TINGKAT_IJAZAH,FS_NM_JABATAN,FS_KD_JENIS_JABATAN,FS_NM_GRUP_BAGIAN,
                h.FS_KD_GRUP_BAGIAN,i.*
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_RPENDIDIKAN_PEG d ON a.FS_KD_PEG=d.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_PENDIDIKAN e ON d.FS_KD_TINGKAT_IJAZAH=e.FS_KD_PENDIDIKAN
                LEFT JOIN HOSPITAL.dbo.TD_RJABATAN_PEG f ON a.FS_KD_PEG=f.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_JABATAN g ON g.FS_KD_JABATAN=f.FS_KD_JABATAN
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN TAD_IPK i ON a.FS_KD_PEG=i.FS_KD_PEG
                WHERE i.FS_KD_TRS = ? AND d.FB_AKTIF='True' AND f.FB_AKTIF = 'True'";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }*/
    
    function get_data_peg_by_search_all($params) {
        $sql = "SELECT FS_NM_PEG,a.FS_KD_PEG,FS_NM_BAGIAN,FS_KD_GOLONGAN,FS_REK_BANK,
                DATEDIFF(year, c.FD_TGL_DINAS, GETDATE()) AS MasaKerja,
                FS_NM_GRUP_BAGIAN,
                h.FS_KD_GRUP_BAGIAN,i.*
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN TAD_IPK i ON a.FS_KD_PEG=i.FS_KD_PEG
                WHERE a.FS_KD_BAGIAN LIKE ? AND a.FB_AKTIF_DINAS = '1'
                AND PERIODE = ? AND TAHUN = ?";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    /*function get_data_peg_by_search_all($params) {
        $sql = "SELECT FS_NM_PEG,a.FS_KD_PEG,FS_NM_BAGIAN,FS_KD_GOLONGAN,FS_REK_BANK,
                DATEDIFF(year, c.FD_TGL_KONTRAK_I, GETDATE()) AS MasaKerja,FS_NM_PENDIDIKAN,
                d.FS_KD_TINGKAT_IJAZAH,FS_NM_JABATAN,FS_KD_JENIS_JABATAN,FS_NM_GRUP_BAGIAN,
                h.FS_KD_GRUP_BAGIAN,i.*
                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_RPENDIDIKAN_PEG d ON a.FS_KD_PEG=d.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_PENDIDIKAN e ON d.FS_KD_TINGKAT_IJAZAH=e.FS_KD_PENDIDIKAN
                LEFT JOIN HOSPITAL.dbo.TD_RJABATAN_PEG f ON a.FS_KD_PEG=f.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_JABATAN g ON g.FS_KD_JABATAN=f.FS_KD_JABATAN
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN TAD_IPK i ON a.FS_KD_PEG=i.FS_KD_PEG
                WHERE a.FS_KD_BAGIAN LIKE ? AND d.FB_AKTIF='True' AND f.FB_AKTIF = 'True'";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }*/

    //get by id for searching
    function cek_data_input($params) {
        $sql = "SELECT * FROM TAD_IPK
                WHERE FS_KD_PEG = ? AND PERIODE = ? AND TAHUN = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->num_rows();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    function cek_data_input_jml($params) {
        $sql = "SELECT * FROM TAD_IPK2
                WHERE PERIODE = ? AND TAHUN = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->num_rows();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    //get all data for pagination
    function get_ipk_by_search($params) {
        $sql = "SELECT a.*,b.FS_NM_PEG,FS_NM_BAGIAN 
                FROM TAD_IPK a
                LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN c ON b.FS_KD_BAGIAN=c.FS_KD_BAGIAN
                WHERE a.FS_KD_PEG = ? AND PERIODE = ? AND TAHUN = ?
                ORDER BY b.FS_KD_BAGIAN ASC, FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    
    function get_jmlipk_by_search($params) {
        $sql = "SELECT SUM(FN_JML_IPK) 'JML_IPK'
                FROM TAD_IPK
                WHERE PERIODE = ? AND TAHUN = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    function get_jmlpdpt_by_search($params) {
        $sql = "SELECT *
                FROM TAD_IPK2
                WHERE PERIODE = ? AND TAHUN = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    function get_bagian() {
        $sql = "SELECT *
                FROM HOSPITAL.dbo.TD_BAGIAN
                ORDER BY FS_NM_BAGIAN ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
}
