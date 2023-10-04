<?php

class m_kepeg_cuti extends CI_Model {

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

    function insert($params) {
        $sql = "INSERT INTO HOSPITAL.dbo.td_trs_cuti (fs_kd_trs, fd_tgl_trs, fs_jam_trs, fs_kd_peg, fd_tgl_mulai,
                fs_jam_mulai, fd_tgl_akhir, fs_jam_akhir, fs_kd_jenis_cuti, fs_keterangan,
                fn_hari, fn_hari_kerja, fd_tgl_perawal, fd_tgl_perakhir, FS_TUGAS,
                fn_sisa_prev, fn_sisa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        return $this->db->query($sql, $params);
    }
    function update($params) {
        $sql = "UPDATE HOSPITAL.dbo.td_trs_cuti SET fd_tgl_trs = ?, fs_jam_trs = ?, fs_kd_peg = ?, fd_tgl_mulai = ?,
                fs_jam_mulai = ?, fd_tgl_akhir = ?, fs_jam_akhir = ?, fs_kd_jenis_cuti = ?, fs_keterangan = ?,
                fn_hari = ?, fn_hari_kerja = ?, fd_tgl_perawal = ?, fd_tgl_perakhir = ?, 
                fn_sisa_prev = ?, fn_sisa = ? WHERE fs_kd_trs = ?";
        return $this->db->query($sql, $params);
    }
    
    function update_status($params) {
        $sql = "UPDATE HOSPITAL.dbo.TD_TRS_ORDER_CUTI SET FB_APPROVED = ?,FS_KD_GEN_ASAL=? WHERE FS_KD_TRS = ?";
        return $this->db->query($sql, $params);
    }

    function update_no_trs_cuti($params) {
        $sql = "UPDATE HOSPITAL.dbo.td_parameter SET fn_kd_cuti = ?";
        return $this->db->query($sql, $params);
    }

    function delete($params) {
        $sql = "UPDATE HOSPITAL.dbo.TD_TRS_CUTI SET FS_KD_PETUGAS_VOID = ?, FD_TGL_VOID = ?
                WHERE FS_KD_TRS = ?";
        return $this->db->query($sql, $params);
    }

    // get task id
    function get_pegawai($params) {
        $sql = "SELECT FS_KD_PEG,FS_NM_PEG
                FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_PEG = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    
    function get_pegawai_by_cuti($params) {
        $sql = "SELECT a.*,b.FS_NM_PEG
                FROM HOSPITAL.dbo.TD_TRS_ORDER_CUTI a
                LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
                WHERE a.FS_KD_TRS = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_peg_by_atasan($params) {
        $sql = "SELECT FS_KD_PEG,FS_NM_PEG
                FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_PEG_ATASAN = ? AND FB_AKTIF_DINAS = '1'
                ORDER BY FS_KD_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_cuti_diambil_by_peg($params) {
        $sql = "SELECT ISNULL(SUM(FN_HARI_KERJA),0) JUMLAH
                FROM HOSPITAL.dbo.TD_TRS_CUTI
                WHERE FD_TGL_VOID='3000-01-01' AND FS_KD_PEG = ?
                AND FD_TGL_PERAWAL = ? AND FD_TGL_PERAKHIR = ?
                AND FS_KD_JENIS_CUTI = 'C01'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_hari_kerja_non_shift($params) {
        $sql = "SELECT COUNT(*) AS 'FN_HARI_KERJA'
                FROM HOSPITAL.dbo.if_get_kalender(?,?) 
                WHERE fn_hari <> '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_hari_kerja_shift($params) {
        $sql = "SELECT COUNT(*) AS 'FN_HARI_KERJA'
                FROM HOSPITAL.dbo.if_get_kalender(?,?)";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_shift_kerja_peg($params) {
        $sql = "SELECT FS_KD_GROUP_SHIFT_KERJA
                FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_PEG = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_hari_raya($params) {
        $sql = "select COUNT(*) AS 'FN_HARI_RAYA'
                from HOSPITAL.dbo.td_raya  
                where fd_tgl_raya between ? AND ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_hari_libur($params) {
        $sql = "SELECT COUNT(*) AS 'FN_HARI_LIBUR'
                FROM HOSPITAL.dbo.TD_JADWAL_GROUP_SHIFT
                WHERE FS_KD_PEG = ? AND 
                FD_TGL_JADWAL BETWEEN ? AND ? AND
                FS_KD_SHIFT_KERJA = 'SL'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_no_trs_cuti($params) {
        $sql = " SELECT RIGHT(fn_kd_cuti+100000000,7)'NO_TRS' FROM HOSPITAL.dbo.td_parameter";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_fn_sisa_prev($params) {
        $sql = "SELECT TOP (1)FN_SISA
                FROM HOSPITAL.dbo.td_trs_cuti
                WHERE FS_KD_PEG = ? AND 
                fd_tgl_perawal = ? AND fd_tgl_perakhir = ?
                ORDER BY fs_kd_trs DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_history_cuti_peg_all($params) {
        $sql = "SELECT a.*,b.FS_NM_PEG,c.FS_NM_JENIS_CUTI,d.FS_KD_TRS 'TransaksiCuti'
                FROM HOSPITAL.dbo.TD_TRS_ORDER_CUTI a
                LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_JENIS_CUTI c ON a.FS_KD_JENIS_CUTI=c.FS_KD_JENIS_CUTI
                left join HOSPITAL.dbo.TD_TRS_CUTI d 
                ON a.FS_KD_PEG=d.FS_KD_PEG AND a.FS_KD_JENIS_CUTI=d.FS_KD_JENIS_CUTI and a.FD_TGL_MULAI=d.FD_TGL_MULAI 
                and a.FD_TGL_AKHIR=d.FD_TGL_AKHIR 
                WHERE d.FD_TGL_VOID = '3000-01-01'
                ORDER BY FD_TGL_MULAI DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_history_cuti_peg_by_atasan($params) {
        $sql = "SELECT DISTINCT a.FS_KD_PEG,b.FS_NM_PEG
        FROM HOSPITAL.dbo.TD_TRS_ORDER_CUTI a
        LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
        LEFT JOIN HOSPITAL.dbo.TD_JENIS_CUTI c ON a.FS_KD_JENIS_CUTI=c.FS_KD_JENIS_CUTI
        WHERE b.FS_KD_PEG_ATASAN = ? AND FD_TGL_VOID = '3000-01-01'
        AND FD_TGL_PERAWAL = ? AND FD_TGL_PERAKHIR = ? ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_history_cuti_peg($params) {
        $sql = "SELECT a.*,b.FS_NM_PEG,c.FS_NM_JENIS_CUTI
                FROM HOSPITAL.dbo.TD_TRS_ORDER_CUTI a
                LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_JENIS_CUTI c ON a.FS_KD_JENIS_CUTI=c.FS_KD_JENIS_CUTI AND a.FD_TGL_VOID = '3000-01-01'
                WHERE b.FS_KD_PEG_ATASAN = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    function get_unit($params) {
        $sql = "SELECT *
                FROM HOSPITAL.dbo.TD_BAGIAN
                ORDER BY FS_NM_BAGIAN ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    function get_peg_by_bagian($params) {
        $sql = "SELECT FS_KD_PEG,FS_NM_PEG
                FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_BAGIAN = ? AND FB_AKTIF_DINAS = '1'
                ORDER BY FS_KD_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_jenis_cuti($params)
    {
        $sql = "SELECT *
                FROM HOSPITAL.dbo.TD_JENIS_CUTI
                WHERE FB_AKTIF = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
}
