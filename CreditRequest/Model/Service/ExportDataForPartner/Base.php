<?php

namespace App\modules\CreditRequest\Model\Service\ExportDataForPartner;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//collections
//use \App\modles\Client\Collection\Client as CollectionClient;

//models
use \App\modules\Core\Model\Base as BaseModel;

//service
use \App\modules\CreditRequest\Model\Service\BaseMail as CreditRequestMailService;
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use Log;
//vendor
use \League\Csv\Writer as CsvWriter;


abstract class Base extends BaseModel {
    use Singleton;

    public $folderPath;
    public $enabled;
    public $sender;
    public $recipient;
    public $mailBlockName;
    public $mailSubject = false;

    public function __construct() {
        $this->init();
    }

    abstract public function init();
    abstract public function getFileName();
    abstract public function getExportData();
    abstract public function getExportDataHeader();
    abstract public function additionalFileProcessing($csv, $filePath);

    public function process() {
        //generate csv file
        $filePath = $this->generateCSV();
		
        //send csv file
        $result = $this->sendViaEmail($filePath);

        return $result;
    }

    public function generateCSV() {
        $fileName = $this->getFileName();
        $filePath = sprintf('%s/%s', $this->folderPath, $fileName);
        //get data
        $data = $this->getExportData();
		
        if(!count($data)) {
            //TODO: log [no data to export, return]
            return false;
        }

        //save data
        $csv = CsvWriter::createFromPath($filePath, "w");
        $csv->setDelimiter(';');
//        $csv->setEnclosure(chr(0));

        $csv->insertOne($this->getExportDataHeader()); //header
        $csv->insertOne($data);

        $this->additionalFileProcessing($csv, $filePath);

        return $filePath;
    }

    public function sendViaEmail($filePath) {
        $creditRequest = $this->getCreditRequest();
        if(!$creditRequest){
            $creditRequest=$this->creditRequest;
        }
        // $client = CollectionClient::getInstance()->load($creditRequest->getKid());
        $client = \App\Client::where('id',$creditRequest->kid)->first();
        $mailService = CreditRequestMailService::getInstance();
        if($this->mailBlockName=='\App\modules\CreditRequest\Block\Mail\ExportDataForPartner\Planfinanz24'){
            $recipient=$this->recipient;
        }else{
            $recipient=$client->email;
        }

		if($this->sender!=='info@credicom.de'){
			$rewriteSender=$this->sender;
		}
		else{
			$rewriteSender='info@credicom.de';
		}
        $mailBlock = new $this->mailBlockName([
            'client' => $client,
            'recipient' => $recipient/*new BaseModel([
                'email' => $this->recipient,
            ])*/,
            'rewriteSender' => 'info@credicom.de',//$this->sender
            'attachment' => $filePath
        ]);
        if($this->mailSubject) {
            $mailBlock->theme = $this->mailSubject;
        }
        $mailSendResult = $mailService->send($mailBlock);
        $creditRequestService = CreditRequestService::getInstance();

        if($mailSendResult) {
            //add credit request note about sent email  

            $creditRequestorType = ($this->getIsBoth()) ? 'both' : (($this->getIsMainApplicant() === false) ? 'coApplicant' : 'mainApplicant');

            $creditRequestService->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s" with data for %s', $this->recipient, $mailBlock->creditRequestNote, $creditRequestorType),
                $creditRequest
            );
            return true;
        } else {
            //add credit request note about error
            $errorMessage = $mailService->getErrorMessage();
            $creditRequestService->addCreditRequestNote($errorMessage, $creditRequest);
            return false;
        }
    }


    public function prepareStringData($string, $addslashes = true) {
        $result = '';

        $result = iconv('UTF-8', "iso-8859-1", trim($string));
        if($addslashes)
            $result = addslashes($result);

        return $result;
    }

    public function prepareDateData($date, $format) {
        return ($date != '0000-00-00') ? date($format, strtotime($date)) : null;
    }

}
