<?php

class m_data_kepeg_jm extends CI_Model {

	function __construct() {
        //Call the Model constructor
		parent::__construct();
	}

	function insert($params) {
		$sql = "INSERT INTO TAD_TRS_JM
		(FS_KD_PEG,FN_JM_ALL,FN_JM_TAMBAH,FN_JM_TL,FN_JM_NON_PAJAK,FN_JM_BRUTO,FN_DASAR_POT,FN_DASAR_POT_KOM,FN_PAJAK,FN_BAZAIS
		,FN_POTONGAN,FS_KET_POTONGAN,FN_JM_NETTO,bulan,tahun,FD_PERIODE_AWAL,FD_PERIODE_AKHIR,mdd,mdb)
		VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		return $this->db->query($sql, $params);
	}

	function delete($params) {
		$sql = "UPDATE TAD_TRS_JM SET mdd_void = ?, mdb_void = ? WHERE FS_KD_TRS = ?";
		return $this->db->query($sql, $params);
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

	function get_data_pegawai($params) {
		$sql = "SELECT aa.FS_NM_PEG, FS_KD_PEG, FS_REK_BANK 
		FROM HOSPITAL.dbo.td_peg aa 
		WHERE aa.fs_kd_peg = ?";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$query->free_result();
			return $result;
		} else {
			return 0;
		}
	}

	function get_data_dasar_pot_tahun($params) {
		$sql = "SELECT SUM(FN_DASAR_POT) 'DASAR_POT'
		FROM TAD_TRS_JM 
		WHERE FS_KD_PEG = ? AND tahun = ? AND mdd_void = '3000-01-01'";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$query->free_result();
			return $result;
		} else {
			return 0;
		}
	}

	function get_data_jm_all_pegawai($params) {
		$sql = " SELECT aa.fs_kd_petugas_medis, ISNULL(bb.fs_nm_peg, ' ') fs_nm_petugas_medis, 
		SUM(aa.fn_tarif) fn_tarif, SUM(aa.fn_jamed_tarif) fn_jamed_tarif,  bb.FS_REK_BANK, 
		SUM(aa.fn_jamed_bruto) fn_jamed_bruto, 
		CAST(0 as DECIMAL(18,2)) AS fn_tax_50, 
		CAST(0 as DECIMAL(18,2)) AS fn_tax_250, 
		CAST(0 as DECIMAL(18,2)) AS fn_tax_500, 
		CAST(0 as DECIMAL(18,2)) AS fn_tax_over, 
		CAST(0 as DECIMAL(18,2)) AS fn_tax_bruto, 
		CAST(0 as DECIMAL(18,2)) AS fn_tax_netto, 
		CAST(0 as DECIMAL(18,2)) AS fn_bazais, 
		CAST(0 as DECIMAL(18,2)) AS fn_jamed_netto 
		FROM ( 
		SELECT      bb.fs_kd_petugas_medis, 
		aa.fn_total fn_tarif, bb.fn_trs_p fn_jamed_tarif, bb.fn_trs_p / 0.7 fn_jamed_bruto 
		FROM       ( 
		SELECT      aa.fs_kd_trs, aa.fs_keterangan, aa.fn_total, aa.fd_tgl_trs, 
		aa.fs_kd_reg, bb.fs_mr, cc.fs_nm_pasien, bb.fd_tgl_keluar 
		FROM        HOSPITAL.dbo.ta_trs_billing aa 
		LEFT JOIN   HOSPITAL.dbo.ta_registrasi bb ON aa.fs_kd_reg = bb.fs_kd_reg 
		LEFT JOIN   HOSPITAL.dbo.tc_mr cc ON bb.fs_mr = cc.fs_mr 
		WHERE       bb.fd_tgl_keluar BETWEEN ? AND ? 
		) aa 
		LEFT JOIN  ( 
		SELECT      aa.fs_kd_trs, aa.fs_kd_petugas_medis, SUM(fn_trs_p) fn_trs_p 
		FROM        HOSPITAL.dbo.ta_trs_billing2 aa 
		INNER JOIN  HOSPITAL.dbo.ta_trs_billing bb ON aa.fs_kd_trs = bb.fs_kd_trs 
		INNER JOIN  HOSPITAL.dbo.ta_registrasi cc ON bb.fs_kd_reg = cc.fs_kd_reg 
		WHERE       aa.fs_kd_petugas_medis <> ' ' 
		AND cc.fd_tgl_keluar BETWEEN ? AND ? 
		AND aa.fs_kd_jenis_bayar IN ('PDP', 'POT', 'BYL') 
		GROUP BY    aa.fs_kd_trs, aa.fs_kd_petugas_medis 
		) bb ON aa.fs_kd_trs = bb.fs_kd_trs 
		LEFT JOIN  HOSPITAL.dbo.td_peg cc ON bb.fs_kd_petugas_medis = cc.fs_kd_peg 
		WHERE      bb.fs_kd_petugas_medis <> ' ' 
		) aa 
		LEFT JOIN  HOSPITAL.dbo.td_peg bb ON aa.fs_kd_petugas_medis = bb.fs_kd_peg 
		WHERE bb.fs_kd_peg IN(?)  GROUP BY   aa.fs_kd_petugas_medis, bb.fs_nm_peg, bb.FS_REK_BANK 
		ORDER BY        fs_nm_petugas_medis 
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

	function get_all_data_jm_periode($params) {
		$sql = "SELECT a.*, b.FS_NM_PEG,b.FS_REK_BANK 
		FROM TAD_TRS_JM a
		LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
		WHERE bulan = ? AND tahun = ? AND mdd_void = '3000-01-01'
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

	function get_data_jm_by_trs($params) {
		$sql = "SELECT a.*, b.FS_NM_PEG,b.FS_REK_BANK,FS_EMAIL 
		FROM TAD_TRS_JM a
		LEFT JOIN HOSPITAL.dbo.TD_PEG b ON a.FS_KD_PEG=b.FS_KD_PEG
		WHERE a.FS_KD_TRS = ? AND mdd_void = '3000-01-01'";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			$query->free_result();
			return $result;
		} else {
			return 0;
		}
	}

}