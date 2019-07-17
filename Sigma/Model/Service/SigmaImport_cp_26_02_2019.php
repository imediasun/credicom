<?php

namespace App\modules\Sigma\Model\Service;

use \League\Csv\Reader as CsvReader;

use \App\modules\Sigma\Model\Service\SigmaCheck;
use \App\CreditOrder;
use \App\SigmaOrder;
use \App\ArchiveSigmaAnswer;
use \App\ArchiveSigmaExport;

use Illuminate\Http\Request;


//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Sigma\Model\Reply;
use \App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;

//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Client\Collection\Client as CollectionClient;
use \App\modules\Sigma\Collection\Reply as CollectionReply;
use \App\modules\CreditRequest\Collection\CreditRequestStatus as CollectionCreditRequestStatus;

//services and stuff
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\Sigma\Model\Service as SigmaService;

use \App\modules\CreditRequest\Model\Service\Mail as CreditRequestMailService;
use \App\modules\CreditRequest\Block\Mail\SalaryCertificateRequest as BlockMailSalaryCertificateRequest;
use \App\modules\CreditRequest\Block\Mail\SigmaBankYellowNotification as BlockMailSigmaBankYellowNotification;

use \App\modules\CreditRequest\Model\Service\Sms as CreditRequestSmsService;
use \App\modules\CreditRequest\Block\Sms\SalaryCertificateRequest as BlockSmsSalaryCertificateRequest;

use \App\modules\CreditRequest\Model\Service\Epost as EpostService;
use \App\modules\CreditRequest\Block\Epost\SalaryCertificateRequest as BlockEpostSalaryCertificateRequest;
use \App\modules\CreditRequest\Model\Tcpdf\Epost as EpostPdf;






class SigmaImport_cp_26_02_2019 extends BaseModel
{
    use Singleton;
	
    public static function process($file_path)
    {
          $file_name = 'sigma_answers_' . date('YmdHis') . '_' . rand(100, 999) . '.csv';
        $folder=sprintf('%s/files/cache/sigma/archive/import',base_path() );
  $processFilePath=sprintf('%s/%s', $folder, $file_name);
  rename($file_path, $processFilePath);

		
			$archive=new ArchiveSigmaAnswer;
		
		$archive->filename=$file_name;
		//$archive->count_records=count($data);
		$archive->save();		
		
		    $result = self::readerCSV($processFilePath);			
			
            self::dbUpdater($result, $archive->id);			
		
		
        return true;
    }


	
	
    public static function processUpload(Request $request)
    {
		if (!$request->hasFile("filename")) return false;
$file = $request->file('filename');
  if ($file->getClientOriginalExtension()!='csv') return false;

          $file_name = 'sigma_import_' . date('YmdHis') . '_' . rand(100, 999) . '.csv';
        $folder=sprintf('%s/files/cache/sigma/archive/import',base_path() );
  $processFilePath=sprintf('%s/%s', $folder, $file_name);
  
  $file->move($folder, $file_name);
		
			$archive=new ArchiveSigmaAnswer;
		
		$archive->filename=$file_name;
		//$archive->count_records=count($data);
		$archive->save();
	
		
		    $result = self::readerCSV($processFilePath);
			
			
            self::dbUpdater($result, $archive->id);

		
        return true;
    }
	
	
	
    public static function reset()
    {

		
		ArchiveSigmaExport::truncate();
		
		ArchiveSigmaAnswer::truncate();
		
		SigmaOrder::truncate();
		
		$credits=CreditOrder::all();
		
		foreach($credits as $credit){
		SigmaCheck::process($credit->id);	
			
		}
		
			

        return true;
    }


	
	    public static function readerCSV($processFilePath)
    {
        $fileName = $processFilePath;

        $csv = CsvReader::createFromPath($fileName, 'r');
        $csv->setDelimiter(';');

        $csvKeys = ['last_name', 'first_name', 'date_birth', 'id', 'answerA', 'answerB', 'answerC'];
        $result = [];
        //dd('$csv->fetchAssoc($csvKeys)',$csv->fetchAssoc($csvKeys));
        foreach ($csv->fetchAssoc($csvKeys) as $row) {

            $item = array_map(function ($value) {
//                $value = iconv("iso-8859-1", 'UTF-8', $value);
                return trim($value);
            }, $row);

            //skip empty rows
            $isEmpty = true;
            foreach ($item as $k => $v) {
                if (!empty($v)) $isEmpty = false;
            }

            if ($isEmpty) continue;
            $result[] = $item;
        }

		//dd($result);
		
        return $result;
    }


    public static function getReplyType($reply)
    {

        $a = $reply['answerA'];
        $b = $reply['answerB'];
        $c = $reply['answerC'];

        if (
            (empty($a) && empty($b) && empty($c)) ||
            (empty($a) && empty($b) && strtoupper($c) == 'A') ||
            (!empty($a) && empty($b) && strtoupper($c) == 'A')
        ) {
            return 0;
        }

        if (
			(!empty($a) && !empty($b) && empty($c))
        ) {
            return 1;
        }

        if (
			(!empty($a) && empty($b) && empty($c))
        ) {
            return 4;
        }

        if (
            (empty($a) && empty($b) && strtoupper($c) != 'A') ||
            (!empty($a) && empty($b) && (strtoupper($c) != 'A'))
        ) {
            return 2;
        }

        return 2;
    }


    public static function dbUpdater($result, $archive_sigma_answer_id)
    {

        foreach ($result as $item) {

            try {

                $sigmaOrder = SigmaOrder::find($item['id']);

                if (!($sigmaOrder == null)) {
					
                    $a = $item['answerA'];
                    $b = $item['answerB'];
                    $c = $item['answerC'];
					
                    $sigmaOrder->archive_sigma_answer_id = $archive_sigma_answer_id;
                    $sigmaOrder->date_answer=date("Y-m-d H:i:s",time());
					$sigmaOrder->answer_a=$a; 
					$sigmaOrder->answer_b=$b; 
					$sigmaOrder->answer_c=$c;
					
					
					$reply_result=self::getReplyType($item);
					
					$sigmaOrder->result=$reply_result==4?1:self::getReplyType($item); 

                    $sigmaOrder->save();

					        switch($reply_result) {
            case 0:
                self::processReplyGreen($sigmaOrder);
                break;
            case 1:
                self::processReplyYellow($sigmaOrder);
                break;
            case 2:
                self::processReplyRed($sigmaOrder);
                break;
            case 4:
                self::processReplyYellow2($sigmaOrder);
            default: break;
        }
					
                }

            } catch (Exception $e) {
                //echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
               
            }

        }


    }

	
	    public static function processReplyGreen($sigmaOrder)
    {
		$credit_order_id=$sigmaOrder->credit_order_id;
		$credit_order_kid=$sigmaOrder->credit_order->kid;
		$credit_order_type=$sigmaOrder->credit_order_type;		
		
		
		//dump('GREEN');
        $creditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($credit_order_id);
        $client = \App\modules\Client\Collection\Client::getInstance()->load($credit_order_kid);

        //update status
        //CreditRequestService::getInstance()->changeInternStatus(ModelCreditRequest::STATUS_WDV_KLARUNG, $creditRequest);

        //add credit request note from sigma
		$ma=($credit_order_type==1) ? $ma='CoApplicant' : '';
					
		
        \App\modules\CreditRequest\Model\Service::getInstance()->addCreditRequestNote('Sigma Bank: '.$ma.' Positiv', $creditRequest);
	
 
    }
	
	   public static function processReplyYellow($sigmaOrder)
    {
		$credit_order_id=$sigmaOrder->credit_order_id;
		$credit_order_kid=$sigmaOrder->credit_order->kid;
		$credit_order_type=$sigmaOrder->credit_order_type;
		
		//dump('YELLOW');
        $creditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($credit_order_id);
        $client = \App\modules\Client\Collection\Client::getInstance()->load($credit_order_kid);
		
        //update status
        CreditRequestService::getInstance()->changeInternStatus(ModelCreditRequest::STATUS_WDV_KLARUNG, $creditRequest);

        //add credit request note from sigma
		$ma=($credit_order_type==1) ? $ma='CoApplicant' : '';
		

		
		
        //add credit request note
        \App\modules\CreditRequest\Model\Service::getInstance()->addCreditRequestNote(
            sprintf('Sigma Bank: '.$ma.' Gelb Fall %s | %s (bitte manuell prüfen)', $sigmaOrder->answer_a, $sigmaOrder->answer_b),
            $creditRequest
        );


    }
	
	
	   public static function processReplyYellow2($sigmaOrder)
    {
		$credit_order_id=$sigmaOrder->credit_order_id;
		$credit_order_kid=$sigmaOrder->credit_order->kid;
		$credit_order_type=$sigmaOrder->credit_order_type;
		
		//dump('YELLOW');
        $creditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($credit_order_id);
        $client = \App\modules\Client\Collection\Client::getInstance()->load($credit_order_kid);
		
        //update status
        CreditRequestService::getInstance()->changeInternStatus(ModelCreditRequest::STATUS_WDV_KLARUNG, $creditRequest);

        //add credit request note from sigma
		$ma=($credit_order_type==1) ? $ma='CoApplicant' : '';
		

		
		
        //add credit request note
        \App\modules\CreditRequest\Model\Service::getInstance()->addCreditRequestNote(
            sprintf('Sigma Bank: '.$ma.' Gelb Fall %s | %s (Amtsgerichtanfrage)', $sigmaOrder->answer_a, $sigmaOrder->answer_b),
            $creditRequest
        );


    }
	
	
    public static function processReplyRed($sigmaOrder)
    {
		$credit_order_id=$sigmaOrder->credit_order_id;
		$credit_order_kid=$sigmaOrder->credit_order->kid;
		$credit_order_type=$sigmaOrder->credit_order_type;
		
	//dump('RED');
        $creditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($credit_order_id);

		$ma=($credit_order_type==1) ? $ma='CoApplicant' : '';
        \App\modules\CreditRequest\Model\Service::getInstance()->addCreditRequestNote('Sigma Bank: '.$ma.' abgelehnt', $creditRequest);
    }
	

}