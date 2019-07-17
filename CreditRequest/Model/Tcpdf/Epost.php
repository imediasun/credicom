<?php
namespace App\modules\CreditRequest\Model\Tcpdf;


class Epost extends \TCPDF
{
    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->init();
    }

    //Page header
    public function Header()
    {
        //Logo
//        $image_file = K_PATH_IMAGES.'logo.jpg';
//        $this->Image($image_file, '', '', 65, '17', 'JPG', '', '', false, 200, 'R', false, false, 0, false, false, false);
//        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
//        $this->Line(15, 23, 195, 23, $style);

    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-38);
        // Set font
        $this->SetFont('helvetica', 'N', 10);
        // Page number
        //$this->Cell(0, 0, 'Dieses Schreiben wurde maschinell erstellt und ist auch ohne Unterschrift gültig.', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function init()
    {
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Credicom');
        $this->SetTitle('datei');
        $this->SetSubject('');
        $this->SetKeywords('');

        // set default header data
        //$this->SetHeaderData('logo.png', 45, '', '');

        // set header and footer fonts
        $this->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $this->SetMargins(20, 42, 22);// 45 //
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $this->SetAutoPageBreak(False, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        /** @var $l array */
        include base_path() . '/tecnick.com/tcpdf/examples/lang/ger.php';
        $this->setLanguageArray($l);

        // ---------------------------------------------------------

        // set default font subsetting mode
        $this->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        //$fontname = $this->addTTFfont(K_PATH_FONTS . 'helvetica.ttf', 'TrueType', 'ansi', 32);
        $this->SetFont('helvetica', '', 10, '', true);
        $this->SetFont('helvetica', '', 10, '', true);
    }
}