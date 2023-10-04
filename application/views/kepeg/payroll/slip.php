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
<page format="230x135" orientation="P" backtop="9mm" backbottom="2mm" backleft="2mm" backright="2mm">
    <page_header>
        <table class="page_header">
            <tr>
                <td style="width:50%;font-size: 10px;text-align: center;">
                    <div>SLIP GAJI <br> Periode <?php
                    if ($results['FN_PERIODE'] == '1') {
                        echo "<b>JANUARI</b> " . "<b>" .date("Y",strtotime($results['xx'])) . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '2') {
                        echo "<b>FEBRUARI</b> " . "<b>" . date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '3') {
                        echo "<b>MARET</b> " . "<b>" . date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '4') {
                        echo "<b>APRIL</b> " . "<b>" . date("Y",strtotime($results['xx'])) . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '5') {
                        echo "<b>MEI</b> " . "<b>" . date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '6') {
                        echo "<b>JUNI</b> " . "<b>" . date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '7') {
                        echo "<b>JULI</b> " . "<b>" . date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '8') {
                        echo "<b>AGUSTUS</b> " . "<b>" .date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '9') {
                        echo "<b>SEPTEMBER</b> " . "<b>" .date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '10') {
                        echo "<b>OKTOBER</b> " . "<b>" . date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '11') {
                        echo "<b>NOVEMBER</b> " . "<b>" .date("Y",strtotime($results['xx']))  . "</b>"  ;
                    } elseif ($results['FN_PERIODE'] == '12') {
                        echo "<b>DESEMBER</b> " . "<b>" .date("Y",strtotime($results['xx'])) . "</b>"  ;
                    }
                    ?> 
                        <?= ' '.$result['TAHUN']; ?>
                    </div>
                </td>
                <td style="width:50%;text-align: right;">
                    <qrcode value="<?= 'SLIP PEGAWAI RS PKU Muhammadiyah Gamping DG NIP '.$results['FS_KD_PEG']; ?>" ec="H" style="width: 10mm; background-color: white; color: black;"></qrcode>
                </td>
            </tr>
        </table>
    </page_header>
    <table class="content">
        <col style="width: 2%;font-size: 10px;">
        <col style="width: 20%;font-size: 10px;">
        <col style="width: 27%;font-size: 10px;">
        <col style="width: 20%;font-size: 10px;">
        <col style="width: 27%;font-size: 10px;">
        <col style="width: 2%;font-size: 10px;">
        <?php
            $biaya_jabatan = (0.05 * $results['JmlBrutoA']);
                    
        ?>
        <tbody>
            <tr>
                <td></td>
                <td>NIK</td>
                <td> : <?= $results['FS_KD_PEG']; ?></td>
                <td>Bagian</td>
                <td> : <?= $results['FS_NM_BAGIAN']; ?> </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Nama</td>
                <td> : <?= $results['FS_NM_PEG']; ?></td>
                <td>Masa Kerja</td>
                <td> : <?= $res['MasaKerja']; ?> </td>  
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Pangkat / Gol</td>
                <td style="border-bottom: 1px #000000 solid">
                    : <?= $results['FS_KD_GOLONGAN']; ?>
                </td>
                <td style="border-bottom: 1px #000000 solid">No Bank</td>
                <td style="border-bottom: 1px #000000 solid">: <?= $results['FS_REK_BANK']; ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="content">
        <col style="width: 2%;font-size: 11px;">
        <col style="width: 20%;font-size: 11px;">
        <col style="width: 27%;font-size: 11px;">
        <col style="width: 20%;font-size: 11px;">
        <col style="width: 27%;font-size: 11px;">
        <col style="width: 2%;font-size: 11px;">
        <tbody>
            <tr>
                <td></td>
                <td colspan="2" style="border-bottom: 1px #000000 solid"><b>PENGHASILAN (A)</b></td>
                <td colspan="2" style="border-bottom: 1px #000000 solid"><b>POTONGAN RESMI (B)</b></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid; padding-bottom:0px">Gaji Pokok </td>
                <td style="border-bottom: 1px #000000 solid; text-align:right;">  <?=number_format($results['FN_GAPOK'], 0, ".", ".") ?></td>
                <td style="border-bottom: 1px #000000 solid">PPH 21</td>
                <td style="border-bottom: 1px #000000 solid; text-align:right;"> <?= number_format($results['FN_PPH_21'], 0, ".", "."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Tj. Suami/Istri</td>
                <td style="border-bottom: 1px #000000 solid; text-align:right;"> <?= number_format($results['FN_TUNJ_SUAMI'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">BPJS KES RS</td>
                <td style="border-bottom: 1px #000000 solid; text-align:right;"><?= number_format($results['FN_TUNJ_BPJS'], 0, ".", "."); ?> </td>
              
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Tj. Anak</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?=number_format($results['FN_TUNJ_ANAK'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">BPJSTK PEG </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_BPJS_TK'], 0, ".", "."); ?></td>
               
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Tj. Beras</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_BERAS'], 0, ".", "."); ?> </td>

                <td style="border-bottom: 1px #000000 solid">BPJSTK RS</td>   
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_BPJS_TK'], 0, ".", "."); ?></td> 
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Tj. Fungsional</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_FUNGSIONAL'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Dapenmuh PEG</td>   
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_DAPENMUH'], 0, ".", "."); ?></td> 
                
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Tj. Jabatan  </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_JABATAN'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Dapenmuh RS</td>   
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_DAPENMUH'], 0, ".", "."); ?></td> 
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid;">Tj. Dapenmuh  </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_DAPENMUH'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid;"><b>Jumlah(B)</b></td>   
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><b><?= number_format($results['TotalPotB'], 0, ".", "."); ?></b></td> 
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Profesi </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_PROFESI'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>    
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">THD</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_THD'] + $results['FN_TUNJ_THD_MAN'] , 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"><b>Potongan(D)</b></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">TKP </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_TUNJ_TKP'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">BPD Konven</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_BRI'], 0, ".", "."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Tj. Makan</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;">  <?= number_format($results['FN_TUNJ_MAKAN'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">BPD Syariah</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_BPD'], 0, ".", "."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Lembur </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_TUNJ_LEMBUR'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">BSM</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_BSM'], 0, ".", "."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid;">Pendidikan </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_PENDIDIKAN'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Barokah</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_BAROKAH'], 0, ".", "."); ?></td>
                
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">IPK </td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid">Farmasi</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($results['FN_FARMASI'], 0, ".", "."); ?></td>
             
                <td></td>
            </tr>
            
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">BPJS KES RS </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_BPJS'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Al-Ikhlas </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_AL_IKHLAS'], 0, ".", "."); ?></td>
               
               
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">BPJSTK RS </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_BPJS_TK'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Perumahan </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_PERUMAHAN'], 0, ".", "."); ?></td>
                
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Rapel</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_RAPEL'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Infaq PP </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_INFAQ_PP'], 0, ".", "."); ?></td>
                
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Pulsa</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_PULSA'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid">Lain-lain </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_LAIN_LAIN'], 0, ".", "."); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Cuti Besar</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_CUTI'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Hari Raya</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_IHR'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
         
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">ONCALL</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_ONCALL'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
         
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Pendapatan Lain</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"><?= number_format($results['FN_TUNJ_RAPELKE'], 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
         
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid"> <b>Jml Bruto(A)</b> </td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <b> <?= number_format($results['JmlBrutoA'], 0, ".", "."); ?> </b> </td>
                <td style="border-bottom: 1px #000000 solid"><b>Jumlah (D)</b></td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <b><?= number_format($results['TotalPotD'], 0, ".", "."); ?> </b> </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Sebelum Pajak </td>
                <td style="border-bottom: 1px #000000 solid">:</td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Biaya Jabatan</td>
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <?= number_format($biaya_jabatan, 0, ".", "."); ?></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">Terima Netto</td>
                <td style="border-bottom: 1px #000000 solid">:</td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">PTKP <?= $jk['FB_JNS_KELAMIN']?></td>
                <td style="border-bottom: 1px #000000 solid; text-align:right">
                  <?php
                        //echo $results['JumlahAnak']
                    if($results['FS_KD_STATUS_KAWIN_DK']==2 && $jk['FB_JNS_KELAMIN']==1) {
                            echo '54.000.000';
                        }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==2 && $results['FB_JNS_KELAMIN']==0 && $results['JumlahAnak']==0) {
                        echo '58.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==2 && $results['FB_JNS_KELAMIN']==0 && $results['JumlahAnak']==1) {
                        echo '63.000.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==2 && $results['FB_JNS_KELAMIN']==0 && $results['JumlahAnak']==2) {
                        echo '67.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==2 && $results['FB_JNS_KELAMIN']==0 && $results['JumlahAnak']==3) {
                        echo '72.000.000';
                    } 
                    else if($results['FS_KD_STATUS_KAWIN_DK']==2 && $results['FB_JNS_KELAMIN']==0 && $results['JumlahAnak']>3) {
                        echo '72.000.000';
                    } 
                    else if($results['FS_KD_STATUS_KAWIN_DK']==3 && $results['JumlahAnak']==1) {
                        echo '58.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==3 && $results['JumlahAnak']==2) {
                        echo '63.000.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==3 && $results['JumlahAnak']==3) {
                        echo '67.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==3 && $results['JumlahAnak']>3) {
                        echo '67.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==4 && $results['JumlahAnak']==1) {
                        echo '58.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==4 && $results['JumlahAnak']==2) {
                        echo '63.000.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==4 && $results['JumlahAnak']==3) {
                        echo '67.500.000';
                    }
                    else if($results['FS_KD_STATUS_KAWIN_DK']==4 && $results['JumlahAnak']>3) {
                        echo '67.500.000';
                    }
                    else 
                    {
                        echo '54.000.000';
                    }
                    
                    ?>


                    
            </td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid">PKP</td>
                <td style="border-bottom: 1px #000000 solid">:</td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid"><b>Gaji Bersih(C) = (A-B)</b></td>
               
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <b><?= number_format($results['JmlBrutoA'] -$results['TotalPotB'], 0, ".", "."); ?></b> </td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-bottom: 1px #000000 solid"><b>Terima Bersih (C-D)</b></td>
              
                <td style="border-bottom: 1px #000000 solid;text-align:right;"> <b><?= number_format($results['JmlBrutoA'] - $results['TotalPotB'] - $results['TotalPotD'], 0, ".", "."); ?></b> </td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td style="border-bottom: 1px #000000 solid"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><b>KETERANGAN RAPEL</b></td>
                <td>
                    <?= $results['FN_KET_RAPEL']?> <br/>
                    <?= $results['FS_RAPELKE_KET']?>
                </td>
                <td><b>KETERANGAN POT</b></td>
                <td><?= $results['FN_KET_POT']?></td>
                <td></td>
            </tr>
          
        </tbody>
    </table>
    <br>
    <!--<em style="font-size: 11px;">*Jika kenaikan/penurunan lebih dari 25% dibandingkan dengan IPK sebelumnya, maka ada faktor koreksi</em>
    <br>
    <em style="font-size: 11px;">*Jika pegawai tidak masuk kerja selama 1 (satu) bulan atau lebih (karena cuti, diklat, profesi dll) selama periode IPK maka perolehan IPK  tidak penuh.l-</em>-->
</page>