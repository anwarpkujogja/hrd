<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH.'plugins/html2pdf/html2pdf.class.php' );

class CI_Html2pdf extends HTML2PDF {

    function CI_Html2pdf() {
        // tcpdf constructor
       parent::__construct($sens = 'P', $format = 'A4', $langue='en', $unicode=true, $encoding='UTF-8', $marges = array(5, 5, 5, 8));

    }
}
// END TCPDF Class
