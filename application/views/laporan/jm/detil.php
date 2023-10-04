<style type="text/Css">
    <!--
    table.page_header {
        width: 100%;
        //border-bottom: solid 1px #000000;
        padding-bottom: 5px;
    }
    table.page_header h2 {
        margin: 0 20px 0 0;
        padding: 0 20px 5px 5px;
    }
    table.page_header img {
        float: left;
    }
    table.page_header h5 {
        float: left;
        margin: 0 20px 0 0;
        padding: 0 20px 5px 5px;
    }
    table.content {
        width: 100%;
        margin : 0px;
        border-collapse: collapse;
        border: solid 1px #000000;
    }
    td {
        vertical-align:top;
        padding: 3px 5px;
    }
    -->
</style>

<page backtop="30mm" backbottom="10mm" backleft="2mm" backright="2mm">
 <page_header>
 <table class="page_header">
    <tr>
        <td style="width:10%;">
            <img src="<?php base_url() ?>resource/doc/images/icon/logo.png" width="70" height="70" />
        </td>
        <td style="width:45%;text-align: left;">
            <h3>RS PKU Muhammadiyah Gamping</h3>
            <h5>Jl. Wates Km 5,5 Ambarketawang, Gamping, Sleman></h5>
        </td>
    </tr>
</table>
</page_header>
<page_footer>
<table style="width: 100%; border-top: solid 1px black;">
    <tr>
        <td style="text-align: left; width: 80%">&nbsp;</td>
        <td style="text-align: right; width: 20%">Halaman [[page_cu]]/[[page_nb]]</td>
    </tr>
</table>
</page_footer>
<table class="content" border="1">
    <tbody>
        <col style="width: 100%;font-size: 12px;">
        <tr>
            <td align="center">
                <br><strong>Rincian Jasa Medis</strong>
            </td>
        </tr>
    </tbody>
</table>
<table class="content">
    <col style="width: 25%;font-size: 10px;">
    <col style="width: 25%;font-size: 10px;">
    <col style="width: 25%;font-size: 10px;">
    <col style="width: 25%;font-size: 10px;">
    <tbody>
        <tr>
            <td style="border-left:solid 1px #000000; ">Kode Dokter </td>
            <td>
                : <?= $rs_dokter['FS_KD_PEG']; ?>
            </td>
            <td>Nama Dokter </td>
            <td style="border-right:solid 1px #000000;">
                : <?= $rs_dokter['FS_NM_PEG']; ?>
            </td>
        </tr>
        <tr>
            <td style="border-left:solid 1px #000000;border-bottom:solid 1px #000000;">Periode</td>
            <td style="border-bottom:solid 1px #000000;">
                : <?= $rs_dokter['FD_PERIODE_AWAL']; ?> s/d <?= $rs_dokter['FD_PERIODE_AKHIR']; ?> 
            </td>
            <td style="border-bottom:solid 1px #000000;">No Rekekning </td>
            <td style="border-right:solid 1px #000000;border-bottom:solid 1px #000000;">
                : <?= $rs_dokter['FS_REK_BANK']; ?>
            </td>
        </tr>
    </tbody>
</table>
<br><br>
<span>Total : <?= number_format($rs_dokter['FN_JM_NETTO']);?></span>
<br><br>
<table class="content">
    <col style="width: 5%;font-size: 10px;">
    <col style="width: 7%;font-size: 10px;">
    <col style="width: 27%;font-size: 10px;">
    <col style="width: 7%;font-size: 10px;">
    <col style="width: 27%;font-size: 10px;">
    <col style="width: 13%;font-size: 10px;">
    <col style="width: 7%;font-size: 10px;">
    <col style="width: 7%;font-size: 10px;">
    <tbody>
        <tr>
            <td style="border:solid 1px #000000;">No</td>
            <td style="border:solid 1px #000000;">Tanggal</td>
            <td style="border:solid 1px #000000;">Tindakan</td>
            <td style="border:solid 1px #000000;">Kode Reg</td>
            <td style="border:solid 1px #000000;">Pasien</td>
            <td style="border:solid 1px #000000;">Layanan</td>
            <td style="border:solid 1px #000000;">Tgl Keluar</td>
            <td style="border:solid 1px #000000;">Jasa Medis</td>
        </tr>
        <?php 
        $no = 1;
        foreach($rs_result as $data){ 
            ?>

            <tr>
                <td style="border:solid 1px #000000;"><?= $no++;?></td>
                <td style="border:solid 1px #000000;"><?= $data['fd_tgl_trs']; ?></td>
                <td style="border:solid 1px #000000;"><?= $data['fs_keterangan']; ?></td>
                <td style="border:solid 1px #000000;"><?= $data['fs_kd_reg']; ?></td>
                <td style="border:solid 1px #000000;"><?= $data['fs_nm_pasien']; ?></td>
                <td style="border:solid 1px #000000;"><?= $data['fs_nm_layanan']; ?></td>
                <td style="border:solid 1px #000000;"><?= $data['fd_tgl_keluar']; ?></td>
                <td style="border:solid 1px #000000;text-align: right;"><?= number_format($data['fn_jamed_tarif']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</page>
