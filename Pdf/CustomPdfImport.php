<?php

namespace App\modules\Pdf;

//use Elibyy\TCPDF\TCPDF;
use Elibyy\TCPDF\TCPDF;
use TCPDF_PARSER as PdfParser;

class CustomPdfImport extends TCPDF {

	/**
	 * Import an existing PDF document
	 * @param $filename (string) Filename of the PDF document to import.
	 * @return true in case of success, false otherwise
	 * @public
	 * @since 1.0.000 (2011-05-24)
	 */
	public function importPDF($filename) {
  //dd($filename);

		// load document
		$rawdata = file_get_contents($filename);
		if ($rawdata === false) {
			$this->Error('Unable to get the content of the file: '.$filename);
		}
		// configuration parameters for parser
		$cfg = array(
			'die_for_errors' => false,
			'ignore_filter_decoding_errors' => true,
			'ignore_missing_filter_decoders' => true,
        );
        
      
		try {
			// parse PDF data
			$pdf = new PdfParser($rawdata, $cfg);
		} catch (Exception $e) {
			dd($e->getMessage());
		}
		// get the parsed data
		$data = $pdf->getParsedData();
		// release some memory
		unset($rawdata);

dd($data);

		// ...

//return $data;

		//print_r($data); // DEBUG


		//unset($pdf);
	}

} // END OF CLASS

//============================================================+
// END OF FILE
//============================================================+
