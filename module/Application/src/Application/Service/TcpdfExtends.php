<?php
namespace Application\Service;

use TCPDF;

class TcpdfExtends extends TCPDF {
    
    public function setSchemeName($header,$logo) {
	$this->header = $header;
	$this->logo = $logo;
    }
	
    //Page header
    public function Header() {
        // Logo
        if(trim($this->logo)!="" && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo)){
	    $image_file = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo;
	    $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo,PATHINFO_EXTENSION));
	    $this->Image($image_file, 12,3,20, '',strtoupper($extension), '', 'T', false, 300, 'L', false, false, 0, false, false, false);
	}
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->writeHTMLCell(0,'',33,10,$this->header,0,1,false,true,'C',true);
        $this->writeHTMLCell(180,'',15,20,'<br/><hr/>',0,1,false,true,'C',true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}