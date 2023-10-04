<?php

class m_lap_jm extends CI_Model {

	function __construct() {
        //Call the Model constructor
		parent::__construct();
	}

	function get_data_dokter($params) {
		$sql = "SELECT DISTINCT aa.fs_kd_peg, fs_nm_alias, fs_nm_peg 
		FROM HOSPITAL.dbo.td_peg aa 
		INNER JOIN HOSPITAL.dbo.td_peg_sat_tugas bb ON aa.fs_kd_peg = bb.fs_kd_peg 
		WHERE aa.fb_aktif_dinas = 1 AND bb.fs_kd_sat_tugas = 'MD'
		ORDER BY fs_nm_peg ASC";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			$query->free_result();
			return $result;
		} else {
			return array();
		}
	}

		function get_data_dokter2($params) {
		$sql = "SELECT a.*, b.FS_NM_PEG,b.FS_REK_BANK 
		FROM TAD_TRS_JM a
		LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
		WHERE a.FS_KD_PEG = ? AND a.FD_PERIODE_AWAL = ? AND a.FD_PERIODE_AKHIR = ?";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$query->free_result();
			return $result;
		} else {
			return 0;
		}
	}

	function get_all_data_jm_periode($params) {
		$sql = "SELECT a.*, b.FS_NM_PEG,b.FS_REK_BANK 
		FROM TAD_TRS_JM a
		LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
		WHERE a.FS_KD_PEG = ? AND tahun = ? AND mdd_void = '3000-01-01'
		ORDER BY bulan ASC";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			$query->free_result();
			return $result;
		} else {
			return array();
		}
	}

	function get_data_dokter_by_trs($params) {
		$sql = "SELECT a.*, b.FS_NM_PEG,b.FS_REK_BANK 
		FROM TAD_TRS_JM a
		LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
		WHERE a.FS_KD_TRS = ? ";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$query->free_result();
			return $result;
		} else {
			return 0;
		}
	}

	function get_data_jm_by_trs($params) {
		$sql = "SELECT *
		FROM TAD_TRS_JM
		WHERE FS_KD_TRS = ?";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$query->free_result();
			return $result;
		} else {
			return 0;
		}
	}

		function get_data_jm_by_rg($params) {
		$sql = " SELECT bb.fs_kd_petugas_medis, ISNULL(cc.fs_nm_peg, ' ') fs_nm_petugas_medis, 
		aa.fs_kd_trs, aa.fd_tgl_trs, aa.fs_keterangan, aa.fs_kd_reg, aa.fs_mr, 
		aa.fd_tgl_keluar, aa.fs_nm_pasien, dd.fs_nm_layanan, ee.fs_nm_kelas, ff.fs_nm_tipe_jaminan, 
		aa.fn_total fn_tarif, bb.fn_trs_p fn_jamed_tarif, bb.fn_trs_p / 0.7 fn_jamed_bruto 
		FROM ( 
		SELECT aa.fs_kd_trs, aa.fs_keterangan, aa.fn_total, aa.fd_tgl_trs, 
		aa.fs_kd_reg, bb.fs_mr, cc.fs_nm_pasien, bb.fd_tgl_keluar, 
		aa.fs_kd_layanan, aa.fs_kd_kelas, bb.fs_kd_tipe_jaminan 
		FROM HOSPITAL.dbo.ta_trs_billing aa 
		LEFT JOIN HOSPITAL.dbo.ta_registrasi bb ON aa.fs_kd_reg = bb.fs_kd_reg 
		LEFT JOIN HOSPITAL.dbo.tc_mr cc ON bb.fs_mr = cc.fs_mr 
		WHERE bb.fd_tgl_keluar BETWEEN ? AND ? 
		) aa 
		LEFT JOIN ( 
		SELECT aa.fs_kd_trs, aa.fs_kd_petugas_medis, 
		SUM(fn_trs_p) fn_trs_p 
		FROM HOSPITAL.dbo.ta_trs_billing2 aa 
		INNER JOIN HOSPITAL.dbo.ta_trs_billing bb ON aa.fs_kd_trs = bb.fs_kd_trs 
		INNER JOIN HOSPITAL.dbo.ta_registrasi cc ON bb.fs_kd_reg = cc.fs_kd_reg 
		WHERE aa.fs_kd_petugas_medis <> ' ' 
		AND cc.fd_tgl_keluar BETWEEN ? AND ? 
		AND aa.fs_kd_jenis_bayar IN ('PDP', 'POT', 'BYL') 
		GROUP BY aa.fs_kd_trs, aa.fs_kd_petugas_medis, bb.fs_kd_layanan,bb.fs_kd_kelas 
		) bb ON aa.fs_kd_trs = bb.fs_kd_trs 
		LEFT JOIN HOSPITAL.dbo.td_peg cc ON bb.fs_kd_petugas_medis = cc.fs_kd_peg 
		LEFT JOIN HOSPITAL.dbo.ta_layanan dd on aa.fs_kd_layanan = dd.fs_kd_layanan 
		LEFT JOIN HOSPITAL.dbo.ta_kelas ee on aa.fs_kd_kelas = ee.fs_kd_kelas 
		LEFT JOIN HOSPITAL.dbo.ta_tipe_jaminan ff ON aa.fs_kd_tipe_jaminan = ff.fs_kd_tipe_jaminan 
		WHERE bb.fs_kd_petugas_medis <> ' ' 
		AND bb.fs_kd_petugas_medis IN(?)
		ORDER BY aa.fd_tgl_trs ASC";
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