<?php
define("ACCESS", "print_sk");
require_once("../../abdidalem/includes/inc_web_init.php");
require_once("../../abdidalem/includes/inc_admin_auth.php");

$datas=fetch("ad_sk","LEFT JOIN ad_profil ON ad_sk.sk_profil_NIK=ad_profil.profil_NIK  LEFT JOIN ad_kawedanan ON ad_kawedanan.kawedanan_code=ad_sk.sk_kawedanan_code LEFT JOIN ad_tepas ON ad_tepas.tepas_code=ad_sk.sk_tepas_code  LEFT JOIN ad_golongan ON ad_golongan.golongan_code=ad_sk.sk_golongan_code LEFT JOIN ad_pangkat ON ad_pangkat.pangkat_code=ad_sk.sk_pangkat_code LEFT JOIN ad_kalenggahan ON ad_kalenggahan.kalenggahan_code=ad_sk.sk_kalenggahan_code LEFT JOIN ad_jenis ON ad_jenis.jenis_code=ad_sk.sk_jenis_code  WHERE sk_id='".substr($_GET["sk_id"],0,10)."'");

$temp_sk=fetch("ad_sk_template", "WHERE  sk_template_code='".$datas["sk_template_code"]."' ");


$strTemp = $temp_sk["sk_template_content"];

$strTemp = str_replace("|::no_sk::|", $datas["sk_nomor"], $strTemp);
$strTemp = str_replace("|::nama::|", $datas["profil_nama"], $strTemp);
$strTemp = str_replace("|::jenis_abdidalem::|", $datas["jenis_title"], $strTemp);
$strTemp = str_replace("|::nama_paringdalem::|", $datas["sk_paringdalem"], $strTemp);
$strTemp = str_replace("|::agama::|", $datas["profil_agama"], $strTemp);
$strTemp = str_replace("|::gelar_pangkat::|", $datas["pangkat_gelar"], $strTemp);
$strTemp = str_replace("|::pangkat::|", $datas["pangkat_title"], $strTemp);
$strTemp = str_replace("|::kalenggahan::|", $datas["kalenggahan_title"], $strTemp);
$strTemp = str_replace("|::golongan::|", $datas["golongan_code"], $strTemp);
$strTemp = str_replace("|::gaji::|", indNumber($datas["golongan_gaji"]), $strTemp);
$strTemp = str_replace("|::sk_title::|", $datas["sk_title"], $strTemp);
$strTemp = str_replace("|::sk_tanggal::|", konv_tanggal($datas["sk_tanggal"]), $strTemp);
$strTemp = str_replace("|::sk_tanggal_penugasan::|", $datas["sk_tanggal_penugasan"], $strTemp);
$strTemp = str_replace("|::sk_tanggal_jawa::|", konv_tanggal($datas["sk_tanggal_jawa"]), $strTemp);
$strTemp = str_replace("|::sk_tanggal_penugasan_jawa::|", $datas["sk_tanggal_penugasan_jawa"], $strTemp);
$strTemp = str_replace("|::sk_kawedanan::|", $datas["kawedanan_title"], $strTemp);
$strTemp = str_replace("|::sk_tepas::|", $datas["tepas_title"], $strTemp);

/*
require("../html2fpdf.php");
	$buffer = ""; 
	$buffer=$strTemp;
	
	$pdf=new HTML2FPDF('P','mm','A4');
	//$pdf->UseCSS(true);
	//$pdf->readInlineCSS($buffer);
	$pdf->SetMargins('20','20','');
	$pdf->SetFont('Arial','',12);
	$pdf->AddPage();
	$pdf->WriteHTML($buffer);
	//$pdf->setFooter(nl2br($langStr[0]['lang_footer']));
	//header('Content-Type: application/pdf');
	$pdf->Output(); //Read the FPDF.org manual to know the other options
	//$pdf->Output("../../../pdf/".$_GET['sk_id'].".pdf", 'F' ); //Read the FPDF.org manual to know the other options
	//header("Location: ../../../pdf/".$_GET['sk_id'].".pdf?".date("Y-m-d H:i:s"));

	//exit;
*/

   // convert to PDF
    require_once(dirname(__FILE__).'/../../html2pdf/html2pdf.class.php');
    try
    {
	
	
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'ISO-8859-15', array('20', '30', '20','20'));
		$html2pdf->setDefaultFont('Arial');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($strTemp);
        $html2pdf->Output("1.pdf");
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }	
?>