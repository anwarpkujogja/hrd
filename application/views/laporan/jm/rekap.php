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
            <h4>RS PKU Muhammadiyah Gamping</h4>
            <h6>Jl. Wates Km 5,5 , Ambarketawang, Gamping, Sleman</h6>
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
                : <?= $rs_dokter['bulan']; ?> - <?= $rs_dokter['tahun']; ?> 
            </td>
            <td style="border-bottom:solid 1px #000000;">No Rekekning </td>
            <td style="border-right:solid 1px #000000;border-bottom:solid 1px #000000;">
                : <?= $rs_dokter['FS_REK_BANK']; ?>
            </td>
        </tr>
    </tbody>
</table>
<br><br>
<table class="content">
    <col style="width: 15%;font-size: 10px;">
    <col style="width: 35%;font-size: 10px;">
    <col style="width: 15%;font-size: 10px;">
    <col style="width: 35%;font-size: 10px;">
    <tbody>
        <tr>
            <td style="border-bottom:solid 1px #000000;text-align: center;font-weight: bold;" colspan="2">Penerimaan</td>
            
            <td style="border-bottom:solid 1px #000000;text-align: center;font-weight: bold;" colspan="2">Potongan</td>
        </tr> <tr>
            <td style="border-bottom:solid 1px #000000;">Jasa Medis</td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"><?= number_format($result['FN_JM_BRUTO']);?></td>
            <td style="border-bottom:solid 1px #000000;">Netto Tax</td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"><?= number_format($result['FN_PAJAK']);?></td>
        </tr>
        <tr>
            <td style="border-bottom:solid 1px #000000;"></td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"></td>
            <td style="border-bottom:solid 1px #000000;">Bazais</td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"><?= number_format($result['FN_BAZAIS']);?></td>
        </tr>
        <tr>
            <td style="border-bottom:solid 1px #000000;"></td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"></td>
            <td style="border-bottom:solid 1px #000000;">Potongan</td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"><?= number_format($result['FN_POTONGAN']);?></td>
        </tr>
        <tr>
            <td style="border-bottom:solid 1px #000000;"></td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"></td>
            <td style="border-bottom:solid 1px #000000;">Keterangan Potongan</td>
            <td style="border-bottom:solid 1px #000000;"><?= $result['FS_KET_POTONGAN'];?></td>
        </tr> 
        <tr>
            <td style="border-bottom:solid 1px #000000;">Diterima</td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"><?= number_format($result['FN_JM_NETTO']);?></td>
            <td style="border-bottom:solid 1px #000000;"></td>
            <td style="border-bottom:solid 1px #000000;text-align: right;"></td>
        </tr>
    </tbody>
</table>
<br><br><br><br>
<table class="content">
    <col style="width: 15%;font-size: 10px;">
    <col style="width: 35%;font-size: 10px;">
    <col style="width: 35%;font-size: 10px;">
    <col style="width: 15%;font-size: 10px;">
    <tbody>
        <tr>
            <td colspan="4" style="text-align:center;">Sleman, <?= date('d F Y', strtotime($rs_dokter['mdd'])); ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                Direktur Umum dan Keuangan
            </td>
            <td style="text-align: center;">
                Manager Akuntansi dan Keuangan
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <qrcode value="drg. Hj Indria Nehriasari,Sp.BM,M.Kes" ec="H" style="width: 15mm; background-color: white; color: black;"></qrcode>
            </td>
            <td style="text-align: center;">
               <qrcode value="Hj Mardiyani,SE" ec="H" style="width: 15mm; background-color: white; color: black;"></qrcode>
           </td>
           <td></td>
       </tr>
       <tr>
        <td></td>
        <td style="text-align: center;">
            drg. Hj Indria Nehriasari,Sp.BM,M.Kes
        </td>
        <td style="text-align: center;">
            Hj Mardiyani,SE
        </td>
        <td></td>
    </tr>
</tbody>
</table>
</page>
