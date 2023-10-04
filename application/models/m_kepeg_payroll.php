<?php

class m_kepeg_payroll extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }


    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    function insert($params) {
        $sql = "INSERT INTO HOSPITAL.dbo.td_trs_cuti (fs_kd_trs, fd_tgl_trs, fs_jam_trs, fs_kd_peg, fd_tgl_mulai,
                fs_jam_mulai, fd_tgl_akhir, fs_jam_akhir, fs_kd_jenis_cuti, fs_keterangan,
                fn_hari, fn_hari_kerja, fd_tgl_perawal, fd_tgl_perakhir, 
                fn_sisa_prev, fn_sisa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->query($sql, $params);
    }
    function update($params) {
        $sql = "UPDATE HOSPITAL.dbo.td_trs_cuti SET fd_tgl_trs = ?, fs_jam_trs = ?, fs_kd_peg = ?, fd_tgl_mulai = ?,
                fs_jam_mulai = ?, fd_tgl_akhir = ?, fs_jam_akhir = ?, fs_kd_jenis_cuti = ?, fs_keterangan = ?,
                fn_hari = ?, fn_hari_kerja = ?, fd_tgl_perawal = ?, fd_tgl_perakhir = ?, 
                fn_sisa_prev = ?, fn_sisa = ? WHERE fs_kd_trs = ?";
        return $this->db->query($sql, $params);
    }
    
    function delete($params) {
        $sql = "UPDATE HOSPITAL.dbo.TD_TRS_CUTI SET FS_KD_PETUGAS_VOID = ?, FD_TGL_VOID = ?
                WHERE FS_KD_TRS = ?";
        return $this->db->query($sql, $params);
    }
    
    function update_no_trs_cuti($params) {
        $sql = "UPDATE HOSPITAL.dbo.td_parameter SET fn_kd_cuti = ?";
        return $this->db->query($sql, $params);
    }

    function get_peg_by_bagian($params) {
        $sql = "SELECT (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + a.FN_TUNJ_BERAS +  a.FN_TUNJ_CUTI +
                        a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI + 
                        a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN + a.FN_TUNJ_TKP + ISNULL(a.FN_TUNJ_OVERTIME,0) +
                        a.FN_TUNJ_PULSA + a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + ISNULL(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK +  a.FN_TUNJ_DAPENMUH +  
                        a.FN_DAPENMUH + a.FN_BRI + a.FN_BPD + a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + 
                        a.FN_INFAQ_PP +  a.FN_LAIN_LAIN + ISNULL(a.FN_BSM,0)) AS TOTAL,
                        c.FS_NM_PEG , a.*
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE   c.FS_KD_BAGIAN LIKE ?  AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'
                ORDER BY c.FS_KD_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //AND YEAR(FD_TANGGAL) = YEAR(GETDATE())

    function get_lembur_peg($params){
        $sql = "SELECT SUM(FN_NOMINAL) AS TOTLEMBUR
                FROM PKU.dbo.TAD_TRS_LEMBUR_PEG 
                WHERE FN_PERIODE = ? AND FS_KD_PEG = ? ;
        ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }


    
    function get_overtime_peg($params){
        $sql = "SELECT SUM(FN_NOMINAL) AS TOTOVERTIME
                FROM PKU.dbo.TAD_TRS_OVERTIME_PEG 
                WHERE FN_PERIODE = ? AND FS_KD_PEG = ? AND
                YEAR(FD_TANGGAL) = YEAR(GETDATE());
        ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_peg_by_name($params) {
        $sql = "SELECT (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + a.FN_TUNJ_BERAS + 
                        a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI + 
                        a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN + a.FN_TUNJ_TKP +
                        a.FN_TUNJ_PULSA + a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + a.FN_TUNJ_RAPEL + ISNULL(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK +  a.FN_TUNJ_DAPENMUH +  
                          a.FN_DAPENMUH + a.FN_BRI + a.FN_BPD + a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + 
                          a.FN_INFAQ_PP +  a.FN_LAIN_LAIN + ISNULL(a.FN_BSM,0)) AS TOTAL 
                            ,c.FS_NM_PEG , c.FS_KD_PEG,  a.*
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE   a.FS_KD_PEG LIKE ?  AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'
                ORDER BY c.FS_KD_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_peg_total($params) {
        $sql = "SELECT (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + 
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI + a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN + a.FN_TUNJ_TKP + a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + ISNULL(a.FN_TUNJ_OVERTIME,0) +
                        a.FN_TUNJ_PULSA + a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + +a.FN_TUNJ_IHR + ISNULL(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK +  a.FN_TUNJ_DAPENMUH +  a.FN_DAPENMUH + a.FN_BRI + a.FN_BPD + a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + a.FN_INFAQ_PP +  a.FN_LAIN_LAIN ) AS TOTAL 
                            ,c.FS_NM_PEG , d.FS_NM_BAGIAN, a.*
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND e.FB_AKTIF = 1 AND a.FN_TYPE = 0
                AND c.FS_KD_STATUS_PEG_RS <> '3'
                ORDER BY d.FS_NM_BAGIAN ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }


    function get_peg_total_magang($params) {
        $sql = "SELECT (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + 
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI + a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN + a.FN_TUNJ_TKP + a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + ISNULL(a.FN_TUNJ_OVERTIME,0) +
                        a.FN_TUNJ_PULSA + a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + +a.FN_TUNJ_IHR + ISNULL(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK +  a.FN_TUNJ_DAPENMUH +  a.FN_DAPENMUH + a.FN_BRI + a.FN_BPD + a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + a.FN_INFAQ_PP +  a.FN_LAIN_LAIN ) AS TOTAL 
                            ,c.FS_NM_PEG , d.FS_NM_BAGIAN, a.*
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND e.FB_AKTIF = 1 AND a.FN_TYPE = 0 
                AND c.FS_KD_STATUS_PEG_RS = '3'

               
                ORDER BY d.FS_NM_BAGIAN ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_peg_total_bonus($params) {
        $sql = "SELECT (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + 
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI + a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN + a.FN_TUNJ_TKP + a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL +
                        a.FN_TUNJ_PULSA + a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + +a.FN_TUNJ_IHR+ ISNULL(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK +  a.FN_TUNJ_DAPENMUH +  a.FN_DAPENMUH + a.FN_BRI + a.FN_BPD + a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + a.FN_INFAQ_PP +  a.FN_LAIN_LAIN ) AS TOTAL 
                            ,c.FS_NM_PEG , d.FS_NM_BAGIAN, a.*
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND e.FB_AKTIF = 1 AND a.FN_TYPE = 1
               
                ORDER BY d.FS_NM_BAGIAN ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_peg_bonus($params) {
        $sql = "SELECT (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + 
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI + a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN + a.FN_TUNJ_TKP + a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL +
                        a.FN_TUNJ_PULSA + a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + +a.FN_TUNJ_IHR+ ISNULL(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK +  a.FN_TUNJ_DAPENMUH +  a.FN_DAPENMUH + a.FN_BRI + a.FN_BPD + a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + a.FN_INFAQ_PP +  a.FN_LAIN_LAIN ) AS TOTAL 
                            ,c.FS_NM_PEG , d.FS_NM_BAGIAN, a.*
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND e.FB_AKTIF = 1 AND a.FS_NM_TYPE = 1
               
                ORDER BY d.FS_NM_BAGIAN ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    
    

    function get_dapenmuh($params) {
        $sql = "SELECT (FN_TUNJ_DAPENMUH + FN_DAPENMUH) AS TOTALDAPEN,
                c.FS_NM_PEG,
                a.* 
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND a.FN_TUNJ_DAPENMUH != 0
                
                ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    
    function get_dapenmuh_excel($params) {
        $sql = "SELECT (FN_TUNJ_DAPENMUH + FN_DAPENMUH) AS TOTALDAPEN,
                c.FS_NM_PEG,
                a.* 
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ?  AND a.FN_TUNJ_DAPENMUH != 0
                
                ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_dapenmuh_periode($params) {
        $sql = "SELECT a.
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1' AND a.FN_TUNJ_DAPENMUH != 0
                
                ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_tot_dapenmuh($params) {
        $sql = "SELECT 
                SUM(a.FN_GAPOK) AS TOTALGAPOK,
                SUM(a.FN_TUNJ_DAPENMUH) AS TOTALTJ,
                SUM(a.FN_DAPENMUH) AS TOTALPEG,
                SUM(a.FN_TUNJ_DAPENMUH) +   SUM(a.FN_DAPENMUH)  AS TOTALIUR
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE a.FN_PERIODE = ? 
                AND YEAR(a.mdd_date) = ?  
                AND a.FN_TUNJ_DAPENMUH != 0
                
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    
    function get_tot_global($params) {
        $sql = "SELECT 
                SUM(a.FN_GAPOK) AS TOTALGAPOK,
                sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                sum(a.FN_TUNJ_ANAK)as TJANAK,
                sum(a.FN_TUNJ_BERAS)as TJBERAS,
                sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) as TJTHD,
                sum(a.FN_TUNJ_TKP) as TJTKP,
                sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                sum(a.FN_TUNJ_LEMBUR + a.FN_TUNJ_LEMBUR_L ) as TJLEMBUR,
                sum(a.FN_TUNJ_IPK) as TJIPK,
                sum(a.FN_TUNJ_CUTI) as TJCUTI,
                sum(a.FN_TUNJ_RAPEL) as TJRAPELS,
                sum(a.FN_INFAQ_PP) as INFAQPP,
                sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                sum(a.FN_TUNJ_IHR) as TJIHR,
                sum(a.FN_TUNJ_OVERTIME) as TJOVERTIME,
                sum(a.FN_TUNJ_PULSA) as TJPULSA,
                sum(a.FN_TUNJ_FUNGSIONAL) as TJFUNGSIONAL,
                sum(a.FN_TUNJ_RAPEL) +  sum(a.FN_TUNJ_ONCALL) + 
                sum(a.FN_TUNJ_PULSA) + sum(a.FN_TUNJ_RAPELKE) as TJRAPEL,
                sum(a.FN_TUNJ_BPJS_TK) as TJBPJSTK,
                sum(a.FN_TUNJ_BPJS) as TJBPJS,
                sum(a.FN_BPJS) as POTBPJS,
                sum(a.FN_PPH_21) as POTPPH21,

                sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) +  sum(a.FN_TUNJ_FUNGSIONAL) +
                    sum(a.FN_TUNJ_JABATAN) +  sum(a.FN_TUNJ_DAPENMUH) +
                    sum(a.FN_TUNJ_PROFESI) +   sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) +
                    sum(a.FN_TUNJ_TKP) + sum(a.FN_TUNJ_MAKAN) +  sum(a.FN_TUNJ_CUTI) +
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) +
                    sum(a.FN_TUNJ_PENDIDIKAN) +  sum(a.FN_TUNJ_IPK) + isnull(sum(a.FN_TUNJ_OVERTIME),0) + 
                    sum(a.FN_TUNJ_BPJS) + sum(a.FN_TUNJ_BPJS_TK) + 
                    sum(a.FN_TUNJ_RAPEL) + isnull(sum(a.FN_TUNJ_RAPELKE),0) + sum(a.FN_TUNJ_PULSA)+
                    sum(a.FN_TUNJ_IHR) +  sum(a.FN_TUNJ_ONCALL)
                     as TOTALBRUTO,

                    (sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) +  sum(a.FN_TUNJ_FUNGSIONAL) +
                    sum(a.FN_TUNJ_JABATAN) +  sum(a.FN_TUNJ_DAPENMUH) + 
                    sum(a.FN_TUNJ_PROFESI) +   sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) +
                    sum(a.FN_TUNJ_TKP) + sum(a.FN_TUNJ_MAKAN) +  sum(a.FN_TUNJ_CUTI) +
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) + 
                    sum(a.FN_TUNJ_PENDIDIKAN) +  sum(a.FN_TUNJ_IPK) +  isnull(sum(a.FN_TUNJ_OVERTIME),0) +
                    sum(a.FN_TUNJ_BPJS) + sum(a.FN_TUNJ_BPJS_TK) + 
                    sum(a.FN_TUNJ_RAPEL) + isnull(sum(a.FN_TUNJ_RAPELKE),0) + sum(a.FN_TUNJ_PULSA)+
                    sum(a.FN_TUNJ_IHR) +  sum(a.FN_TUNJ_ONCALL)) -

                    (sum(a.FN_PPH_21) + sum(a.FN_TUNJ_BPJS) + sum(a.FN_BPJS_TK) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_DAPENMUH) +  sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_BPD) + isnull(sum(a.FN_BRI),0) +  isnull(sum(a.FN_BSM),0) +
                    sum(a.FN_BAROKAH) +  sum(a.FN_FARMASI) + sum(a.FN_AL_IKHLAS) + sum(a.FN_PERUMAHAN) +  
                    sum(a.FN_INFAQ_PP)  + sum(a.FN_LAIN_LAIN))
                    AS TERIMABERSIH
            
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'
              
               
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_tot_tetap($params) {
        $sql = "SELECT 
                SUM(a.FN_GAPOK) AS TOTALGAPOK,
                sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                sum(a.FN_TUNJ_ANAK)as TJANAK,
                sum(a.FN_TUNJ_BERAS)as TJBERAS,
                sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) as TJTHD,
                sum(a.FN_TUNJ_TKP) as TJTKP,
                sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                sum(a.FN_TUNJ_LEMBUR + a.FN_TUNJ_LEMBUR_L) as TJLEMBUR,
                sum(a.FN_TUNJ_IPK) as TJIPK,
                sum(a.FN_TUNJ_CUTI) as TJCUTI,
                sum(a.FN_TUNJ_RAPEL) as TJRAPELS,
                sum(a.FN_INFAQ_PP) as INFAQPP,
                sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                sum(a.FN_TUNJ_IHR) as TJIHR,
                sum(a.FN_TUNJ_PULSA) as TJPULSA,
                sum(a.FN_TUNJ_FUNGSIONAL) as TJFUNGSIONAL,
                sum(a.FN_TUNJ_RAPEL) +  sum(a.FN_TUNJ_ONCALL) + 
                sum(a.FN_TUNJ_PULSA) + sum(a.FN_TUNJ_RAPELKE) as TJRAPEL,
                sum(a.FN_TUNJ_BPJS_TK) as TJBPJSTK,
                sum(a.FN_TUNJ_BPJS) as TJBPJS,
                sum(a.FN_BPJS) as POTBPJS,
                sum(a.FN_PPH_21) as POTPPH21,

                sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) +  sum(a.FN_TUNJ_FUNGSIONAL) +
                    sum(a.FN_TUNJ_JABATAN) +  sum(a.FN_TUNJ_DAPENMUH) +
                    sum(a.FN_TUNJ_PROFESI) +   sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) +
                    sum(a.FN_TUNJ_TKP) + sum(a.FN_TUNJ_MAKAN) +  sum(a.FN_TUNJ_CUTI) +
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) +
                    sum(a.FN_TUNJ_PENDIDIKAN) +  sum(a.FN_TUNJ_IPK) +
                    sum(a.FN_TUNJ_BPJS) + sum(a.FN_TUNJ_BPJS_TK) + 
                    sum(a.FN_TUNJ_RAPEL) + isnull(sum(a.FN_TUNJ_RAPELKE),0) + sum(a.FN_TUNJ_PULSA)+
                    sum(a.FN_TUNJ_IHR) +  sum(a.FN_TUNJ_ONCALL)
                    as TOTALBRUTO,

                    (sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) +  sum(a.FN_TUNJ_FUNGSIONAL) +
                    sum(a.FN_TUNJ_JABATAN) +  sum(a.FN_TUNJ_DAPENMUH) + 
                    sum(a.FN_TUNJ_PROFESI) +   sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) +
                    sum(a.FN_TUNJ_TKP) + sum(a.FN_TUNJ_MAKAN) + 
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) + 
                    sum(a.FN_TUNJ_PENDIDIKAN) +  sum(a.FN_TUNJ_IPK) +  sum(a.FN_TUNJ_CUTI) +
                    sum(a.FN_TUNJ_BPJS) + sum(a.FN_TUNJ_BPJS_TK) + 
                    sum(a.FN_TUNJ_RAPEL) + isnull(sum(a.FN_TUNJ_RAPELKE),0) + sum(a.FN_TUNJ_PULSA)+
                    sum(a.FN_TUNJ_IHR) +  sum(a.FN_TUNJ_ONCALL)) -

                    (sum(a.FN_PPH_21) + sum(a.FN_TUNJ_BPJS) + sum(a.FN_BPJS_TK) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_DAPENMUH) +  sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_BPD) + isnull(sum(a.FN_BRI),0) +  isnull(sum(a.FN_BSM),0) +
                    sum(a.FN_BAROKAH) +  sum(a.FN_FARMASI) + sum(a.FN_AL_IKHLAS) + sum(a.FN_PERUMAHAN) +  
                    sum(a.FN_INFAQ_PP)  + sum(a.FN_LAIN_LAIN))
                    AS TERIMABERSIH
            
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ?  
                AND c.FS_KD_STATUS_PEG_RS = '1'
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_rekapglobal($params) {
        $sql = "SELECT  a.FS_KD_BAGIAN, d.FS_NM_BAGIAN ,
                sum(a.FN_GAPOK)as GAJI,
                sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                sum(a.FN_TUNJ_ANAK)as TJANAK,
                sum(a.FN_TUNJ_BERAS)as TJBERAS,
                sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                sum(a.FN_TUNJ_THD) +  sum(a.FN_TUNJ_THD_MAN) as TJTHD,
                sum(a.FN_TUNJ_TKP) as TJTKP,
                sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                sum(a.FN_TUNJ_LEMBUR + a.FN_TUNJ_LEMBUR_L ) as TJLEMBUR,
                sum(a.FN_TUNJ_BPJS) as POTBPJS,
                sum(a.FN_TUNJ_BPJS_TK) as POTBPJSTK,
                sum(a.FN_TUNJ_IPK) as TJIPK,
                sum(a.FN_TUNJ_CUTI) as TJCUTI,
                sum(a.FN_INFAQ_PP) as INFAQPP,
                sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                sum(a.FN_TUNJ_RAPEL) +  sum(a.FN_TUNJ_ONCALL) + sum(a.FN_TUNJ_LEMBUR) +
                sum(a.FN_TUNJ_LEMBUR_L) + sum(a.FN_TUNJ_PULSA) as TJRAPEL,
                sum(a.FN_TUNJ_IHR) as TJIHR,
                sum(a.FN_TUNJ_FUNGSIONAL) as TJFUNGSIONAL,
                sum(a.FN_TUNJ_OVERTIME) as TJOVERTIME,
                
                    sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) + 
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) + isnull(sum(a.FN_TUNJ_OVERTIME),0) +
                    sum(a.FN_TUNJ_FUNGSIONAL) as TOTALBRUTO,

                    (sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) + sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +  sum(a.FN_TUNJ_OVERTIME) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL)) - (sum(a.FN_INFAQ_PP)+sum(a.FN_PPH_21)) AS TERIMABERSIH,
                sum(a.FN_PPH_21) as PPH21
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1' AND a.FN_TYPE = 0
                GROUP BY a.FS_KD_BAGIAN, d.FS_NM_BAGIAN
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

    function get_data_pegawai2($params){
        $sql = "SELECT a.FS_KD_PEG, 
        a.FS_NM_PEG, 
        a.FS_KD_IDENTITAS,
        h.FS_NM_STATUS_KAWIN_DK,
        k.FS_NM_JENIS_KELAMIN,
        a.FS_GOL_DARAH,
        a.FD_TGL_DINAS,
        a.FS_KD_IDENTITAS,
        j.FS_NM_PENDIDIKAN, 
        a.FD_TGL_LAHIR,
        c.FS_NM_STATUS_PEG_RS,
        b.FS_NM_BAGIAN,
        a.FS_ALM_PEG,
        a.FS_KOTA_PEG,
        a.FS_NO_NPWP,
        a.FS_HP_PEG,
        d.FS_NM_KELURAHAN,
        e.FS_NM_KECAMATAN,
        f.FS_NM_KABUPATEN,
        g.FS_NM_PROPINSI,
        a.FS_EMAIL
     

        FROM HOSPITAL.dbo.TD_PEG AS a
        LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
        LEFT JOIN HOSPITAL.dbo.TD_STATUS_PEG_RS c ON a.FS_KD_STATUS_PEG_RS=c.FS_KD_STATUS_PEG_RS
        LEFT JOIN HOSPITAL.dbo.TA_KELURAHAN d ON d.FS_KD_KELURAHAN=a.FS_KD_KELURAHAN
        LEFT JOIN HOSPITAL.dbo.TA_KECAMATAN e ON e.FS_KD_KECAMATAN=d.FS_KD_KECAMATAN
        LEFT JOIN HOSPITAL.dbo.TA_KABUPATEN f ON f.FS_KD_KABUPATEN=e.FS_KD_KABUPATEN
        LEFT JOIN HOSPITAL.dbo.TA_PROPINSI g ON g.FS_KD_PROPINSI=f.FS_KD_PROPINSI
        LEFT JOIN HOSPITAL.dbo.TA_STATUS_KAWIN_DK h ON h.FS_KD_STATUS_KAWIN_DK =  a.FS_KD_STATUS_KAWIN_DK
        LEFT JOIN HOSPITAL.dbo.TD_RPENDIDIKAN_PEG i ON i.FS_KD_PEG = a.FS_KD_PEG
        LEFT JOIN HOSPITAL.dbo.TD_PENDIDIKAN j ON j.FS_KD_PENDIDIKAN = i.FS_KD_PENDIDIKAN
        LEFT JOIN HOSPITAL.dbo.TA_JENIS_KELAMIN k ON k.FS_KD_JENIS_KELAMIN = a.FS_KD_JENIS_KELAMIN 
        WHERE (a.FS_KD_STATUS_PEG_RS = 1 
        OR a.FS_KD_STATUS_PEG_RS = 2 OR a.FS_KD_STATUS_PEG_RS = 4 OR a.FS_KD_STATUS_PEG_RS = 5 OR a.FS_KD_STATUS_PEG_RS = 8)
        AND a.FB_AKTIF_DINAS <> 0 AND i.FB_AKTIF = 1

        ORDER BY a.FS_NM_PEG
    ";

  $query = $this->db->query($sql,$params);
  if ($query->num_rows() > 0) {
    $result = $query->result_array();
    $query->free_result();
    return $result;
  } else {
    return array();
  }
}

    function get_rekapglobal_excel($params) {
        $sql = "SELECT  a.FS_KD_BAGIAN, d.FS_NM_BAGIAN , sum(a.FN_GAPOK)as GAJI,
                sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                sum(a.FN_TUNJ_ANAK)as TJANAK,
                sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                sum(a.FN_TUNJ_BERAS)as TJBERAS,
                sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                sum(a.FN_TUNJ_BPJS) as TJBPJS,
                sum(a.FN_TUNJ_BPJS_TK) as TJBPJSTK,
                sum(a.FN_TUNJ_FUNGSIONAL) as TJFUNGSIONAL,
                sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                sum(a.FN_TUNJ_THD) as TJTHD,
                sum(a.FN_TUNJ_LEMBUR + a.FN_TUNJ_LEMBUR_L) as TJLEMBUR,
                sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                sum(a.FN_TUNJ_TKP) as TJTKP,
                sum(a.FN_TUNJ_IPK) as TJIPK,
                sum(a.FN_TUNJ_CUTI) as TJCUTI,
                sum(a.FN_TUNJ_RAPEL)  as TJRAPEL,
                sum(a.FN_INFAQ_PP) as INFAQPP,             
                sum(a.FN_TUNJ_IHR) as TJIHR,
                sum(a.FN_TUNJ_PULSA) as TJPULSA,
            
                sum(a.FN_TUNJ_RAPEL) as TJRAPEL,
                    sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL) as TOTALBRUTO,

                    (sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) + sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL)) - (sum(a.FN_INFAQ_PP)+sum(a.FN_PPH_21)) AS TERIMABERSIH,
                sum(a.FN_PPH_21) as PPH21
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1' AND a.FN_TYPE = '0'
                GROUP BY a.FS_KD_BAGIAN, d.FS_NM_BAGIAN
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

    
    function get_periode_global($params) {
        $sql = "SELECT  a.FN_PERIODE
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
             ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_periode_tetap($params) {
        $sql = "SELECT  a.FN_PERIODE
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
             ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_periode_dapenmuh($params) {
        $sql = "SELECT  a.FN_PERIODE
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
             ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    


    function get_rekaptetap($params) {
        $sql = "SELECT  a.FS_KD_BAGIAN, d.FS_NM_BAGIAN ,
                sum(a.FN_GAPOK)as GAJI,
                sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                sum(a.FN_TUNJ_ANAK)as TJANAK,
                sum(a.FN_TUNJ_BERAS)as TJBERAS,
                sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                sum(a.FN_TUNJ_THD) +  sum(a.FN_TUNJ_THD_MAN) as TJTHD,
                sum(a.FN_TUNJ_TKP) as TJTKP,
                sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                sum(a.FN_TUNJ_LEMBUR + a.FN_TUNJ_LEMBUR_L) as TJLEMBUR,
                sum(a.FN_TUNJ_BPJS) as POTBPJS,
                sum(a.FN_TUNJ_BPJS_TK) as POTBPJSTK,
                sum(a.FN_TUNJ_IPK) as TJIPK,
                sum(a.FN_TUNJ_CUTI) as TJCUTI,
                sum(a.FN_INFAQ_PP) as INFAQPP,
                sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                sum(a.FN_TUNJ_RAPEL) +  sum(a.FN_TUNJ_ONCALL) + sum(a.FN_TUNJ_LEMBUR) +
                sum(a.FN_TUNJ_LEMBUR_L) + sum(a.FN_TUNJ_PULSA) as TJRAPEL,
                sum(a.FN_TUNJ_IHR) as TJIHR,
                sum(a.FN_TUNJ_FUNGSIONAL) as TJFUNGSIONAL,
                
                    sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL) as TOTALBRUTO,

                    (sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) + sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL)) - (sum(a.FN_INFAQ_PP)+sum(a.FN_PPH_21)) AS TERIMABERSIH,
                sum(a.FN_PPH_21) as PPH21
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1' AND (c.FS_KD_STATUS_PEG_RS = '1' OR c.FS_KD_STATUS_PEG_RS = '4')AND a.FN_TYPE = '0'
                GROUP BY a.FS_KD_BAGIAN, d.FS_NM_BAGIAN
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


    function get_rekaptetap_excel($params) {
        $sql = "SELECT  a.FS_KD_BAGIAN, d.FS_NM_BAGIAN , sum(a.FN_GAPOK)as GAJI,
                sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                sum(a.FN_TUNJ_ANAK)as TJANAK,
                sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                sum(a.FN_TUNJ_BERAS)as TJBERAS,
                sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                sum(a.FN_TUNJ_BPJS) as TJBPJS,
                sum(a.FN_TUNJ_BPJS_TK) as TJBPJSTK,
                sum(a.FN_TUNJ_FUNGSIONAL) as TJFUNGSIONAL,
                sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                sum(a.FN_TUNJ_THD) as TJTHD,
                sum(a.FN_TUNJ_LEMBUR + a.FN_TUNJ_LEMBUR_L) as TJLEMBUR,
                sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                sum(a.FN_TUNJ_TKP) as TJTKP,
                sum(a.FN_TUNJ_IPK) as TJIPK,
                sum(a.FN_TUNJ_CUTI) as TJCUTI,
                sum(a.FN_TUNJ_RAPEL) as TJRAPEL,
                sum(a.FN_INFAQ_PP) as INFAQPP,             
                sum(a.FN_TUNJ_IHR) as TJIHR,
                sum(a.FN_TUNJ_PULSA) as TJPULSA,
            
                sum(a.FN_TUNJ_RAPEL) as TJRAPEL,
                    sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) +   sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL) as TOTALBRUTO,

                    (sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                    sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) + 
                    sum(a.FN_TUNJ_PROFESI) +  sum(a.FN_TUNJ_TKP) + 
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_PENDIDIKAN) +
                    sum(a.FN_TUNJ_LEMBUR) + sum(a.FN_TUNJ_LEMBUR_L) +  sum(a.FN_TUNJ_BPJS_TK) +
                    sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_MAKAN) +
                    sum(a.FN_TUNJ_FUNGSIONAL)) - (sum(a.FN_INFAQ_PP)+sum(a.FN_PPH_21)) AS TERIMABERSIH,
                sum(a.FN_PPH_21) as PPH21
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'  AND (c.FS_KD_STATUS_PEG_RS = '1' OR c.FS_KD_STATUS_PEG_RS = '4')  AND a.FN_TYPE = '0'
                GROUP BY a.FS_KD_BAGIAN, d.FS_NM_BAGIAN
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


    function get_pegawai($params) {
        $sql = "SELECT a.*, b.*, k.*,
                HOSPITAL.dbo.if_get_umur(FD_TGL_DINAS,?) AS MasaKerja,
                (
                    SELECT COUNT (z.FS_KD_PEG) 
                    FROM HOSPITAL.dbo.TD_PEG z
                    WHERE z.FS_KD_PEG LIKE ? AND z.FB_DITANGGUNG = '1'
                ) AS JumlahAnak,
                (
                    SELECT COUNT (xx.FS_KD_PEG)
                    FROM HOSPITAL.dbo.TD_PEG xx
                    WHERE xx.FS_KD_PEG LIKE ? AND xx.FB_DITANGGUNG = '1'
                ) as TunjSuami,
                (
                    SELECT COUNT (xa.FS_KD_PEG)
                    FROM HOSPITAL.dbo.TD_PEG xa
                    WHERE xa.FS_KD_PEG LIKE ? AND xa.FB_DITANGGUNG = '1'
                ) as TunjIstri

                FROM HOSPITAL.dbo.TD_PEG a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
                LEFT JOIN PKU.dbo.TAD_TRS_PAYROLL f ON a.FS_KD_PEG = f.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG k ON a.FS_KD_PEG = k.FS_KD_PEG 
                WHERE a.FS_KD_PEG = ? ORDER BY FS_KD_TRS_KENAIKAN DESC ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    public function getdata(){
        
    }

    function get_THD($params){
        $sql= " SELECT     ss.fs_kd_peg, fs_nm_peg, 
             (HOSPITAL.dbo.if_get_jadwal_kerja(ss.fs_kd_peg, ? , ? ))fn_hari_kerja,  
             (HOSPITAL.dbo.if_get_day_msk_telat(ss.fs_kd_peg, ? , ? ))fn_msk_terlambat, 
             (HOSPITAL.dbo.if_get_day_keluar_awal(ss.fs_kd_peg,? , ? ))fn_plg_lebih_awal, 
             (HOSPITAL.dbo.if_get_day_tidak_hadir(ss.fs_kd_peg, ? , ? ))fn_tidak_hadir,  
             (HOSPITAL.dbo.if_get_hari_cuti_ALL(ss.fs_kd_peg, ? , ? ))fn_hari_cuti, 
             (HOSPITAL.dbo.if_get_day_pagi(ss.fs_kd_peg, ? , ? ))fn_jml_jaga_pagi, 
             (HOSPITAL.dbo.if_get_day_siang(ss.fs_kd_peg, ? , ? ))fn_jml_jaga_siang, 
             (HOSPITAL.dbo.if_get_day_malam(ss.fs_kd_peg, ? , ? ))fn_jml_jaga_malam, 
             (HOSPITAL.dbo.if_get_day_shift(ss.fs_kd_peg, ? , ? ,'P',1,0))fn_jml_msk_pgi_tepat, 
             (HOSPITAL.dbo.if_get_day_shift(ss.fs_kd_peg, ? , ? ,'S',1,0))fn_jml_msk_siang_tepat, 
             (HOSPITAL.dbo.if_get_day_shift(ss.fs_kd_peg, ? , ? ,'M',1,0))fn_jml_msk_mlm_tepat, 
             (HOSPITAL.dbo.if_get_day_shift(ss.fs_kd_peg, ? , ? ,'P',1,1))fn_jml_msk_pgi_tepat_k, 
             (HOSPITAL.dbo.if_get_day_shift(ss.fs_kd_peg, ? , ? ,'S',1,1))fn_jml_msk_siang_tepat_k, 
             (HOSPITAL.dbo.if_get_day_shift(ss.fs_kd_peg, ? , ? ,'M',1,1))fn_jml_msk_mlm_tepat_k, 
             fs_keterangan, 0 as fn_thd,  
             isnull(fn_dinas,0)fn_dinas_luar, 
             isnull(fn_total,0)fn_total 
            FROM HOSPITAL.dbo.td_peg ss 
            LEFT JOIN  (SELECT fs_keterangan, fs_kd_peg 
                        FROM HOSPITAL.dbo.td_jadwal_group_shift 
                        WHERE fd_tgl_jadwal = ? )  bb on ss.fs_kd_peg = bb.fs_kd_peg 
            LEFT JOIN   (SELECT  fs_kd_peg, SUM(fn_total) as fn_total, 
                                        COUNT(fs_kd_trs) As fn_dinas 
                                FROM        HOSPITAL.dbo.td_trs_cuti 
                                WHERE       fs_kd_jenis_cuti='D01' 
                                AND         fd_tgl_trs between ? AND ? 
                                GROUP BY    fs_kd_peg, fn_total) cc ON ss.fs_kd_peg = cc.fs_kd_peg 
            WHERE ss.fs_kd_peg = ? AND  ss.fb_aktif_dinas=1";
            $query = $this->db->query($sql, $params);
               if ($query->num_rows() > 0) {
                   $result = $query->row_array();
                   $query->free_result();
                   return $result;
               } else {
                   return 0;
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

    function get_person($params) {
        $sql = "SELECT FS_NM_PEG
                FROM HOSPITAL.dbo.TD_PEG 
                WHERE FS_KD_STATUS_PEG_RS = 1 OR FS_KD_STATUS_PEG_RS = 2 
               
                ORDER BY FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    function get_list_pendapatan($params) {
        $sql = "SELECT *
                FROM PKU.dbo.TAD_COM_PDP_GAJI
                WHERE FB_AKTIF = 1
                ORDER BY FS_KD_TRS ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    function get_list_pengurangan($params) {
        $sql = "SELECT *
                FROM PKU.dbo.TAD_COM_PENGURANGAN_GAJI
                WHERE FB_AKTIF = 1
                ORDER BY FS_KD_TRS ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

          // insert payroll
     function insert_payroll($params) { //39
      $sql = "INSERT INTO TAD_TRS_PAYROLL (FS_KD_PEG, FN_PERIODE, FN_GAPOK, FN_TUNJ_SUAMI, FN_TUNJ_ANAK,
                FN_TUNJ_DAPENMUH, FN_TUNJ_BERAS, FN_TUNJ_JABATAN, FN_TUNJ_BPJS, FN_TUNJ_BPJS_TK,
                FN_TUNJ_FUNGSIONAL, FN_TUNJ_PROFESI, FN_TUNJ_THD, FN_TUNJ_LEMBUR, FN_TUNJ_PENDIDIKAN,
                FN_TUNJ_MAKAN, FN_TUNJ_TKP, FN_PPH_21, FN_BPJS, FN_BPJS_TK,
                FN_BRI, FN_BPD, FN_BAROKAH, FN_FARMASI, FN_AL_IKHLAS, 
                FN_PERUMAHAN, FN_INFAQ_PP, FN_DAPENMUH, FN_LAIN_LAIN, mdb,
                mdd_date, mdd_time, FS_KD_BAGIAN, FN_TUNJ_IPK, FN_TUNJ_CUTI,
                FN_TUNJ_RAPEL, FN_TUNJ_IHR, MaKer, FN_TUNJ_PULSA, FN_TUNJ_ONCALL, 
                FN_TUNJ_LEMBUR_L, FN_TUNJ_THD_MAN, FN_KET_RAPEL, FN_KET_POT, FN_PTKP, FN_TUNJ_OVERTIME) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?)";
      return $this->db->query($sql, $params);
    }

  
    function get_payroll_perpegawai($params){
        $sql = "SELECT a.*, c.FS_NM_PEG
        FROM PKU.dbo.TAD_TRS_PAYROLL a
        LEFT JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG = c.FS_KD_PEG
        WHERE a.FS_KD_PAYROLL = ?"; 
    
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function get_lembur_perpegawai($params){
        $sql = "SELECT a.*, c.FS_NM_PEG, b.FS_NM_BAGIAN, c.FS_KD_GOLONGAN, c.FS_REK_BANK
        FROM PKU.dbo.TAD_TRS_LEMBUR_PEG a
        LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
        LEFT JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG = c.FS_KD_PEG
        WHERE a.FS_KD_PAYROLL = ?"; 
    
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    function getdata_payroll_perpegawai($params){
        $sql = "SELECT a.*, b.*, c.*, f.*, h.*, f.FN_PTKP, e.FS_KD_GOLONGAN, f.mdd_date AS xx,

                (
                   SELECT COUNT (z.FS_KD_PEG) 
                   FROM HOSPITAL.dbo.TD_PEG z
                   WHERE z.FS_KD_PEG LIKE ? AND z.FB_DITANGGUNG = '1'
                ) AS JumlahAnak,

        (f.FN_GAPOK + f.FN_TUNJ_SUAMI + f.FN_TUNJ_ANAK +  f.FN_TUNJ_BERAS + f.FN_TUNJ_DAPENMUH  + f.FN_TUNJ_JABATAN + f.FN_TUNJ_BPJS 
        + f.FN_TUNJ_BPJS_TK + f.FN_TUNJ_FUNGSIONAL + f.FN_TUNJ_PROFESI + f.FN_TUNJ_THD  + f.FN_TUNJ_THD_MAN + f.FN_TUNJ_LEMBUR + f.FN_TUNJ_PENDIDIKAN + FN_TUNJ_MAKAN + FN_TUNJ_TKP + FN_TUNJ_IPK
        + f.FN_TUNJ_CUTI + f.FN_TUNJ_RAPEL + f.FN_TUNJ_PULSA + f.FN_TUNJ_IHR + f.FN_TUNJ_ONCALL + ISNULL(f.FN_TUNJ_OVERTIME,0) + f.FN_TUNJ_LEMBUR_L +  ISNULL(f.FN_TUNJ_RAPELKE,0)
        ) AS JmlBrutoA,

        (f.FN_TUNJ_BPJS + f.FN_TUNJ_BPJS_TK + f.FN_TUNJ_FUNGSIONAL) AS TotBPJSTKFUNG,
        (f.FN_BPJS + f.FN_BPJS_TK) AS BPJSTot,

        (f.FN_TUNJ_LEMBUR + f.FN_TUNJ_LEMBUR_L) AS LemburTot, f.FN_BONUS_BRUTO,
        
        (ISNULL(f.FN_DAPENMUH,0) + ISNULL(f.FN_PPH_21,0) + ISNULL(f.FN_BPJS_TK,0) + ISNULL(f.FN_TUNJ_BPJS_TK,0) + ISNULL(f.FN_TUNJ_DAPENMUH,0) + ISNULL(f.FN_TUNJ_BPJS,0)) AS TotalPotB,
        
        (ISNULL(f.FN_BRI,0) + ISNULL(f.FN_BPD,0) + ISNULL(f.FN_BAROKAH,0) + ISNULL(f.FN_FARMASI,0) + ISNULL(f.FN_AL_IKHLAS,0) + ISNULL(f.FN_PERUMAHAN,0) + ISNULL(f.FN_INFAQ_PP,0) + ISNULL(f.FN_LAIN_LAIN,0) + ISNULL(f.FN_BSM,0)) AS TotalPotD
                    
        FROM HOSPITAL.dbo.TD_PEG a
        LEFT JOIN HOSPITAL.dbo.TD_BAGIAN b ON a.FS_KD_BAGIAN=b.FS_KD_BAGIAN
        LEFT JOIN HOSPITAL.dbo.TD_PEG_STATUS c ON a.FS_KD_PEG=c.FS_KD_PEG
        LEFT JOIN HOSPITAL.dbo.TD_GRUP_BAGIAN h ON h.FS_KD_GRUP_BAGIAN=b.FS_KD_GRUP_BAGIAN
        LEFT JOIN PKU.dbo.TAD_TRS_PAYROLL f ON a.FS_KD_PEG = f.FS_KD_PEG
        LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
        WHERE f.FS_KD_PAYROLL = ? AND e.FB_AKTIF=1"; 
    
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result; 
        } else {
            return array();
        }
    }


    function get_jenis_kelamin($FS_KD_PEG){
        $sql = "SELECT FB_JNS_KELAMIN,FS_NM_ALIAS
                FROM HOSPITAL.dbo.TD_PEG 
                WHERE FS_KD_PEG = ?
                ";
         $query = $this->db->query($sql, $params);
         if ($query->num_rows() > 0) {
             $result = $query->row_array();
             $query->free_result();
             return $result; 
         } else {
             return array();
         }
    }

        //update data
    function update_payroll($params) {
        $sql = "UPDATE PKU.dbo.TAD_TRS_PAYROLL
                SET FS_KD_PEG = ?, FN_PERIODE = ?,  FN_GAPOK = ?, FN_TUNJ_SUAMI = ?, 
                    FN_TUNJ_ANAK = ?, FN_TUNJ_DAPENMUH = ?, FN_TUNJ_BERAS = ?, FN_TUNJ_JABATAN=?,
                    FN_TUNJ_BPJS = ?,FN_TUNJ_BPJS_TK = ?, FN_TUNJ_FUNGSIONAL = ?, FN_TUNJ_PROFESI = ?,
                    FN_TUNJ_THD = ?, FN_TUNJ_LEMBUR = ?, FN_TUNJ_PENDIDIKAN = ?, FN_TUNJ_MAKAN = ?, 
                    FN_TUNJ_TKP = ?, FN_PPH_21 = ?, FN_BPJS = ?, FN_BPJS_TK = ?, 
                    FN_BRI = ?, FN_BPD = ?, FN_BAROKAH = ?, FN_FARMASI = ?, 
                    FN_AL_IKHLAS = ?, FN_PERUMAHAN = ?, FN_INFAQ_PP = ?, FN_DAPENMUH = ?,
                    FN_LAIN_LAIN = ?, mdb = ?, mdd_date = ?, mdd_time = ?, 
                    FS_KD_BAGIAN = ?, FN_TUNJ_IPK=?,  FN_TUNJ_CUTI=?,  FN_TUNJ_RAPEL=?, 
                    FN_TUNJ_IHR=?, FN_TUNJ_PULSA=?, FN_TUNJ_ONCALL=?, FN_TUNJ_LEMBUR_L = ?,
                    FN_TUNJ_THD_MAN = ?, FN_KET_RAPEL = ?, FN_KET_POT = ?, FN_TUNJ_RAPELKE = ?, 
                    FS_RAPELKE_KET = ?, FN_BSM  = ?, FN_TUNJ_OVERTIME  = ? , FN_PTKP  = ?
                    
                WHERE FS_KD_PAYROLL = ?";
        return $this->db->query($sql, $params);
    }

    function potong_payroll($params) {
        $sql = "UPDATE PKU.dbo.TAD_TRS_PAYROLL
                SET FN_PPH_21 = ?, FN_BPJS = ?, FN_BPJS_TK = ?, FN_BRI = ?, FN_BPD = ?, 
                    FN_BAROKAH = ?, FN_FARMASI = ?, FN_AL_IKHLAS = ?,  FN_PERUMAHAN = ?, 
                    FN_INFAQ_PP = ?, FN_DAPENMUH = ?,FN_LAIN_LAIN = ?,  FN_KET_POT = ?, 
                    FN_BSM = ?
                    
                WHERE FS_KD_PAYROLL = ?";
        return $this->db->query($sql, $params);
    }


    
    function pendapatan_payroll($params) {
        $sql = "UPDATE PKU.dbo.TAD_TRS_PAYROLL
                SET FN_TUNJ_RAPELKE = ?, FS_RAPELKE_KET = ?
                    
                WHERE FS_KD_PAYROLL = ?";
        return $this->db->query($sql, $params);
    }


    function delete_payroll($params) {
        $sql = "DELETE FROM TAD_TRS_PAYROLL WHERE FS_KD_PAYROLL=?";
        return $this->db->query($sql, $params);
    }

    function delete_lembur_peg($params) {
        $sql = "DELETE FROM TAD_TRS_LEMBUR_PEG WHERE FS_KD_PAYROLL=?";
        return $this->db->query($sql, $params);
    }

    function delete_overtime_peg($params) {
        $sql = "DELETE FROM TAD_TRS_OVERTIME_PEG WHERE FS_KD_OVERTIME=?";
        return $this->db->query($sql, $params);
    }


    function get_rekapexcel($params){
                    $sql = "SELECT  a.FS_KD_BAGIAN, d.FS_NM_BAGIAN , 
                    sum(a.FN_GAPOK)as GAJI,
                    sum(a.FN_TUNJ_SUAMI)as TJISTRI,
                    sum(a.FN_TUNJ_ANAK)as TJANAK,
                    sum(a.FN_TUNJ_BERAS)as TJBERAS,
                    sum(a.FN_TUNJ_DAPENMUH) as TJDAPENMUH,
                    sum(a.FN_TUNJ_JABATAN)as TJJABATAN,
                    sum(a.FN_TUNJ_BPJS)as TJBPJS,
                    sum(a.FN_TUNJ_BPJS_TK)as TJBPJSTK,
                    sum(a.FN_TUNJ_FUNGSIONAL)as TJFUNGSIONAL,
                    sum(a.FN_TUNJ_PROFESI)as TJPROFESI,
                    sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) as TJTHD,
                    sum(a.FN_TUNJ_LEMBUR) + sum(a.FN_TUNJ_LEMBUR_L) as TJLEMBUR,
                    sum(a.FN_TUNJ_PENDIDIKAN) as TJPENDIDIKAN,
                    sum(a.FN_TUNJ_MAKAN) as TJMAKAN,
                    sum(a.FN_TUNJ_TKP) as TJTKP,
                    sum(a.FN_TUNJ_IPK) as TJIPK,
                    sum(a.FN_TUNJ_CUTI) as TJCUTI,
                    sum(a.FN_TUNJ_RAPEL) as TJRAPEL,
                    sum(a.FN_TUNJ_IHR) as TJIHR,
                    sum(a.FN_TUNJ_PULSA) as TJPULSA,
                    sum(a.FN_PPH_21) as POTPPH21,
                    sum(a.FN_BPJS) as POTBPJS,
                    sum(a.FN_BPJS_TK) as POTBPJSTK,
                    sum(a.FN_BRI) as POTBPDKONVEN,
                    sum(a.FN_BPD) as POTBPDSYARIAH,
                    sum(a.FN_BAROKAH) as POTBAROKAH,
                    sum(a.FN_FARMASI) as POTFARMASI,
                    sum(a.FN_AL_IKHLAS) as POTALIKHLAS,
                    sum(a.FN_PERUMAHAN) as POTPERUMAHAN,
                    sum(a.FN_INFAQ_PP) as POTINFAQPP,
                    sum(a.FN_DAPENMUH) as POTDAPENMUH,
                    sum(a.FN_LAIN_LAIN) as POTLAIN,
                    isnull(sum(a.FN_BSM),0) as POTBSM,


                        sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                        sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) +  sum(a.FN_TUNJ_DAPENMUH) +
                        sum(a.FN_TUNJ_BPJS) +  sum(a.FN_TUNJ_BPJS_TK) +  sum(a.FN_TUNJ_FUNGSIONAL) +
                        sum(a.FN_TUNJ_PROFESI) +   sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) +
                        sum(a.FN_TUNJ_LEMBUR) +  sum(a.FN_TUNJ_LEMBUR_L)  +   sum(a.FN_TUNJ_PENDIDIKAN) +
                        sum(a.FN_TUNJ_MAKAN) + sum(a.FN_TUNJ_TKP) +  sum(a.FN_TUNJ_IPK) +  sum(a.FN_TUNJ_CUTI) +
                        sum(a.FN_TUNJ_RAPEL) + sum(a.FN_TUNJ_IHR)+ sum(a.FN_TUNJ_PULSA) + sum(a.FN_TUNJ_RAPELKE)
                 
                        as TOTALBRUTO,

                        ( sum(a.FN_GAPOK) + sum(a.FN_TUNJ_SUAMI) + sum(a.FN_TUNJ_ANAK) + 
                        sum(a.FN_TUNJ_BERAS) + sum(a.FN_TUNJ_JABATAN) +  sum(a.FN_TUNJ_DAPENMUH) +
                        sum(a.FN_TUNJ_BPJS) +  sum(a.FN_TUNJ_BPJS_TK) +  sum(a.FN_TUNJ_FUNGSIONAL) +
                        sum(a.FN_TUNJ_PROFESI) +   sum(a.FN_TUNJ_THD) + sum(a.FN_TUNJ_THD_MAN) +
                        sum(a.FN_TUNJ_LEMBUR) +  sum(a.FN_TUNJ_LEMBUR_L)  +   sum(a.FN_TUNJ_PENDIDIKAN) +
                        sum(a.FN_TUNJ_MAKAN) + sum(a.FN_TUNJ_TKP) +  sum(a.FN_TUNJ_IPK) +  sum(a.FN_TUNJ_CUTI) +
                        sum(a.FN_TUNJ_RAPEL) + sum(a.FN_TUNJ_IHR)+ sum(a.FN_TUNJ_PULSA) + sum(a.FN_TUNJ_RAPELKE)) -  
                        (sum(a.FN_PPH_21) + sum(a.FN_BPJS) +  sum(a.FN_BPJS_TK) +
                        sum(a.FN_BRI) +  sum(a.FN_BPD) +   sum(a.FN_BAROKAH) + sum(a.FN_FARMASI) +   sum(a.FN_AL_IKHLAS) +
                        sum(a.FN_PERUMAHAN) +  sum(a.FN_INFAQ_PP) +  sum(a.FN_DAPENMUH) +  sum(a.FN_LAIN_LAIN) + isnull(sum(a.FN_BSM),0) + 
                        sum(a.FN_TUNJ_DAPENMUH) + sum(a.FN_TUNJ_BPJS_TK) +  sum(a.FN_TUNJ_BPJS)) 
                        
                        AS TERIMABERSIH 
                   
                    FROM PKU.dbo.TAD_TRS_PAYROLL a
                    JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                    LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                    WHERE a.FN_PERIODE = ? AND c.FS_KD_STATUS_PEG_RS = '1' OR c.FS_KD_STATUS_PEG_RS = '4'   AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'
                    GROUP BY a.FS_KD_BAGIAN, d.FS_NM_BAGIAN
                ";
            $query = $this->db->query($sql, $params);
            if ($query->num_rows() > 0) {
                $result = $query->result();
                $query->free_result();
                return $result;
            } else {
                return array();
            }
    }


    
    function get_rekapexcel_unit($params) {
                        $sql = "SELECT(a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)) 
                        - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK + 
                        a.FN_TUNJ_DAPENMUH +  a.FN_BRI + a.FN_BPD + 
                        a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + 
                        a.FN_INFAQ_PP +  a.FN_DAPENMUH + a.FN_LAIN_LAIN + ISNULL(a.FN_BSM,0) ) AS TERIMABERSIH,


                        (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)
                        ) AS JmlBruto,
                       
                        c.FS_NM_PEG ,c.FS_REK_BANK, d.FS_NM_BAGIAN,  a.*
                        FROM PKU.dbo.TAD_TRS_PAYROLL a
                        LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                        LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                        
                        WHERE  c.FS_KD_BAGIAN = ? AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
                        AND c.FB_AKTIF_DINAS = '1'
                        ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }


    function get_rekapexcel_total($params) {
        $sql = "SELECT(a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + isnull(a.FN_TUNJ_OVERTIME,0) + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)) 
                       - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK + 
                          a.FN_TUNJ_DAPENMUH +  a.FN_BRI + a.FN_BPD + 
                          a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + 
                          a.FN_INFAQ_PP +  a.FN_DAPENMUH + a.FN_LAIN_LAIN + ISNULL(a.FN_BSM,0) ) AS TERIMABERSIH,
                        (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + isnull(a.FN_TUNJ_OVERTIME,0) + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)
                        ) AS JmlBruto,
                       
                       c.FS_NM_PEG ,c.FS_REK_BANK, d.FS_NM_BAGIAN,  a.*, e.FS_KD_GOLONGAN
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND  e.FB_AKTIF = '1' AND a.FN_TYPE = 0
                AND c.FS_KD_STATUS_PEG_RS <> '3'
                ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }


    function get_rekapexcel_total_magang($params) {
        $sql = "SELECT(a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + isnull(a.FN_TUNJ_OVERTIME,0) + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)) 
                       - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK + 
                          a.FN_TUNJ_DAPENMUH +  a.FN_BRI + a.FN_BPD + 
                          a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + 
                          a.FN_INFAQ_PP +  a.FN_DAPENMUH + a.FN_LAIN_LAIN + ISNULL(a.FN_BSM,0) ) AS TERIMABERSIH,
                        (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + isnull(a.FN_TUNJ_OVERTIME,0) + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)
                        ) AS JmlBruto,
                       
                       c.FS_NM_PEG ,c.FS_REK_BANK, d.FS_NM_BAGIAN,  a.*, e.FS_KD_GOLONGAN
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND  e.FB_AKTIF = '1' AND a.FN_TYPE = 0
                AND c.FS_KD_STATUS_PEG_RS = '3'
                ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    

    function get_rekapexcel_bonus_total($params) {
        $sql = "SELECT(a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)) 
                       - (a.FN_PPH_21  +  a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK + a.FN_BPJS_TK + 
                          a.FN_TUNJ_DAPENMUH +  a.FN_BRI + a.FN_BPD + 
                          a.FN_BAROKAH + a.FN_FARMASI + a.FN_AL_IKHLAS + a.FN_PERUMAHAN + 
                          a.FN_INFAQ_PP +  a.FN_DAPENMUH + a.FN_LAIN_LAIN + ISNULL(a.FN_BSM,0) ) AS TERIMABERSIH,
                        (a.FN_GAPOK + a.FN_TUNJ_SUAMI + a.FN_TUNJ_ANAK + a.FN_TUNJ_DAPENMUH + 
                        a.FN_TUNJ_BERAS + a.FN_TUNJ_JABATAN + a.FN_TUNJ_BPJS + a.FN_TUNJ_BPJS_TK +
                        a.FN_TUNJ_FUNGSIONAL + a.FN_TUNJ_PROFESI +  a.FN_TUNJ_THD + a.FN_TUNJ_LEMBUR + 
                        a.FN_TUNJ_PENDIDIKAN + a.FN_TUNJ_MAKAN  + a.FN_TUNJ_TKP + a.FN_TUNJ_IPK + 
                        a.FN_TUNJ_CUTI + a.FN_TUNJ_RAPEL + a.FN_TUNJ_IHR + a.FN_TUNJ_PULSA + 
                        a.FN_TUNJ_ONCALL + a.FN_TUNJ_LEMBUR_L + a.FN_TUNJ_THD_MAN + 
                        isnull(a.FN_TUNJ_RAPELKE,0)
                        ) AS JmlBruto,
                       
                       c.FS_NM_PEG ,c.FS_REK_BANK, d.FS_NM_BAGIAN,  a.*, e.FS_KD_GOLONGAN
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                LEFT JOIN HOSPITAL.dbo.TD_RGAJI_BERKALA_PEG e ON e.FS_KD_PEG=a.FS_KD_PEG
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND  e.FB_AKTIF = '1' AND a.FN_TYPE = 1
               
                ORDER BY c.FS_NM_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }
    

    function get_periode_bulan_bagian($params){
        $sql = "SELECT a.FN_PERIODE, d.FS_NM_BAGIAN
                FROM PKU.dbo.TAD_TRS_PAYROLL a
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
                
               ";
                $query = $this->db->query($sql,$params);
                if ($query->num_rows() > 0) {
                    $result = $query->row_array();
                    $query->free_result();
                    return $result;
                } else {
                    return array();
                }
    }

    function get_periode_bulan($params){
        $sql = "SELECT a.FN_PERIODE,  FROM PKU.dbo.TAD_TRS_PAYROLL a
                WHERE  a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ?
                group by fn_periode ";
                $query = $this->db->query($sql,$params);
                if ($query->num_rows() > 0) {
                    $result = $query->row_array();
                    $query->free_result();
                    return $result;
                } else {
                    return array();
                }
    }

    function get_data_peg($params) {
        $sql = "SELECT *,HOSPITAL.dbo.if_get_umur(FD_TGL_DINAS,?) AS MasaKerja FROM HOSPITAL.dbo.TD_PEG
                WHERE FS_KD_PEG = ?";
        $query = $this->db->query($sql,$params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function insert_lembur($params) {
        $sql = "INSERT INTO TAD_TRS_LEMBUR_PEG (FS_KD_PEG, FN_PERIODE, FS_KD_BAGIAN,
                                            FD_TANGGAL, FN_JAM_LEMBUR, FN_JAM_LEMBUR_L, FN_NOMINAL, 
                                            mdd_date, mdd_times, mdb_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->query($sql, $params);
      }

      
    function insert_overtime($params) {
        $sql = "INSERT INTO TAD_TRS_OVERTIME_PEG (FS_KD_PEG, FN_PERIODE, FS_KD_BAGIAN,
                                            FD_TANGGAL, FN_JAM_OVERTIME, FN_NOMINAL, 
                                            mdd_date, mdd_times, mdb_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->query($sql, $params);
      }



      function get_lembur_by_peg($params) {
        $sql = "SELECT a.FS_KD_PAYROLL, a.FS_KD_PEG, c.FS_NM_PEG, a.FN_JAM_LEMBUR, 
                       a.FN_JAM_LEMBUR_L, a.FD_TANGGAL, a.FN_NOMINAL
                FROM PKU.dbo.TAD_TRS_LEMBUR_PEG a
               
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG = c.FS_KD_PEG
                WHERE c.FS_KD_BAGIAN LIKE ? AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
                AND c.FB_AKTIF_DINAS = '1'

                ORDER BY c.FS_KD_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }



    function get_overtime_by_peg($params) {
        $sql = "SELECT a.FS_KD_OVERTIME, a.FS_KD_PEG, c.FS_NM_PEG,
                       a.FN_JAM_OVERTIME, a.FD_TANGGAL, a.FN_NOMINAL
                FROM PKU.dbo.TAD_TRS_OVERTIME_PEG a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG = c.FS_KD_PEG
                WHERE c.FS_KD_BAGIAN LIKE ? AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? 
                AND c.FB_AKTIF_DINAS = '1'

                ORDER BY c.FS_KD_PEG ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_overtime_by_bagian($params) {
        $sql = "SELECT d.FS_NM_BAGIAN, a.FN_PERIODE
                    
                FROM PKU.dbo.TAD_TRS_OVERTIME_PEG a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE c.FS_KD_BAGIAN LIKE ? AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }


    function get_tot_overtime_by_peg($params) {
        $sql = "SELECT sum(a.FN_NOMINAL) AS FN_TOTAL_OVERTIME,
                       sum(a.FN_JAM_OVERTIME) AS FN_TOTAL_JAM_OVERTIME
                FROM PKU.dbo.TAD_TRS_OVERTIME_PEG a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE c.FS_KD_BAGIAN LIKE ?
                AND a.FN_PERIODE = ? 
                AND YEAR(a.mdd_date) = ? 
                AND c.FB_AKTIF_DINAS = '1'
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_tot_lembur_by_peg($params) {
        $sql = "SELECT sum(a.FN_NOMINAL) AS FN_TOTAL_LEMBUR,
                       sum(a.FN_JAM_LEMBUR) AS FN_TOTAL_JAM_BIASA,
                       sum(a.FN_JAM_LEMBUR_L) AS FN_TOTAL_JAM_LIBUR
                    
                FROM PKU.dbo.TAD_TRS_LEMBUR_PEG a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                WHERE c.FS_KD_BAGIAN LIKE ?
                AND a.FN_PERIODE = ? 
                AND YEAR(a.mdd_date) = ? 
                AND c.FB_AKTIF_DINAS = '1'
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }


    function get_lembur_by_bagian($params) {
        $sql = "SELECT d.FS_NM_BAGIAN, a.FN_PERIODE
                    
                FROM PKU.dbo.TAD_TRS_LEMBUR_PEG a
                LEFT OUTER JOIN HOSPITAL.dbo.TD_PEG c ON a.FS_KD_PEG=c.FS_KD_PEG
                LEFT JOIN HOSPITAL.dbo.TD_BAGIAN d ON a.FS_KD_BAGIAN=d.FS_KD_BAGIAN
                WHERE c.FS_KD_BAGIAN LIKE ? AND a.FN_PERIODE = ? AND YEAR(a.mdd_date) = ? AND c.FB_AKTIF_DINAS = '1'
                ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
  

}
