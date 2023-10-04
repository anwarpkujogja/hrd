<!--<style type="text/Css">
    
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
   
</style> -->
<?php date_default_timezone_set('Asia/Jakarta'); ?>
<?php foreach ($rs_result as $result){?>
<page format="A6" orientation="P" backtop="15mm" backbottom="2mm" backleft="2mm" backright="2mm">
   <page_header>
        <table class="page_header">
            <tr>
                <td style="width:50%;font-size: 10px;text-align: center;">
                    <div>SLIP INSENTIF PRESTASI KERJA <br> Periode <?php
                    if ($result['PERIODE'] == '1') {
                        echo "Januari - Maret";
                    } elseif ($result['PERIODE'] == '2') {
                        echo "April - Juni";
                    } elseif ($result['PERIODE'] == '3') {
                        echo "Juli - September";
                    } elseif ($result['PERIODE'] == '4') {
                        echo "Oktober - Desember";
                    }
                    ?> 
                        <?= ' '.$result['TAHUN']; ?>
                    </div>
                </td>
                <td style="width:50%;text-align: right;">
                    <qrcode value="<?= $result['FS_KD_TRS']; ?>" ec="H" style="width: 10mm; background-color: white; color: black;"></qrcode>
                    
                </td>
            </tr>
        </table>
    </page_header>
    <table class="content">
        <col style="width: 5%;font-size: 11px;">
        <col style="width: 18%;font-size: 11px;">
        <col style="width: 27%;font-size: 11px;">
        <col style="width: 18%;font-size: 11px;">
        <col style="width: 27%;font-size: 11px;">
        <col style="width: 5%;font-size: 11px;">
        <tbody>
            <tr>
                <td></td>
                <td>NIK</td>
                <td> : <?= $result['FS_KD_PEG']; ?></td>
                <td>Bagian</td>
                <td> : <?= $result['FS_NM_BAGIAN']; ?> </td>
                <td></td>

            </tr>
            <tr>
                <td></td>
                <td>Nama</td>
                <td>
                    : <?= $result['FS_NM_PEG']; ?>
                </td>
                <td>Masa Kerja</td>
                <td> : <?= $result['MasaKerja']; ?> </td>
                <td></td>

            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Pangkat / Gol</td>
                <td style="border-bottom: 1px #000000 solid">
                    : <?= $result['FS_KD_GOLONGAN']; ?>
                </td>
                <td style="border-bottom: 1px #000000 solid">No Bank</td>
                <td style="border-bottom: 1px #000000 solid">: <?= $result['FS_REK_BANK']; ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="content">
        <col style="width: 5%;font-size: 11px;">
        <col style="width: 20%;font-size: 11px;">
        <col style="width: 25%;font-size: 11px;">
        <col style="width: 20%;font-size: 11px;">
        <col style="width: 25%;font-size: 11px;">
        <col style="width: 5%;font-size: 11px;">
        <tbody>
            <tr>
                <td></td>
                <td>Pendidikan</td>
                <td><?= $result['FS_PENDIDIKAN']; ?></td>
                <td>Jabatan</td>
                <td><?= $result['FS_JABATAN']; ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Risiko Kerja</td>
                <td><?= $result['FS_BAGIAN']; ?></td>
                <td>Masa Kerja</td>
                <td><?= $result['FS_MASA_KERJA']; ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Tugas lain (Panitia, Tim, Komite)</td>
                <td><?= $result['FS_TUGAS_LAIN']; ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Indek Kinerja Unit</td>
                <td><?= $result['FS_IKU']; ?> %</td>
                <td>Indek Kinerja Individu</td>
                <td><?= $result['FS_IKI']; ?> %</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Faktor Pengurang</td>
                <td style="border-bottom: 1px #000000 solid"><?= $result['FS_PENGURANG']; ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">Total Indek</td>
                <td colspan="2"><?= $result['FN_JML_IPK']; ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" style="border-bottom: 1px #000000 solid">Nilai per Indek</td>
                <td colspan="2" style="border-bottom: 1px #000000 solid">Rp. <?= number_format($rs_jmlpdpt['FN_JML_PENDAPATAN']/$rs_jmlindex['JML_IPK'],0,",","."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid" colspan="2">IPK</td>
                <td style="border-bottom: 1px #000000 solid" colspan="2">Rp. <?= number_format($result['FN_JML_TERIMA'],0,",","."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid" colspan="2">Potongan</td>
                <td style="border-bottom: 1px #000000 solid" colspan="2">Rp. <?= number_format($result['FS_POTONGAN'],0,",","."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid" colspan="2">IPK Diterima</td>
                <td style="border-bottom: 1px #000000 solid" colspan="2">Rp. <?= number_format($result['FN_JML_TERIMA']-$result['FS_POTONGAN'],0,",","."); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <br>
    <em>*Jika kenaikan/penurunan lebih dari 25% dibandingkan dengan IPK sebelumnya, maka ada faktor koreksi</em>
</page>
<?php } ?>