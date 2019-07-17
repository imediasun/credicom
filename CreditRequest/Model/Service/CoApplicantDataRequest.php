<?php

namespace App\modules\CreditRequest\Model\Service;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;

//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Client\Collection\Client as CollectionClient;

//services and stuff
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\CreditRequest\Model\Service\Mail as CreditRequestMailService;

use \App\modules\CreditRequest\Model\Service\Sms as CreditRequestSmsService;

use \App\modules\CreditRequest\Model\Service\Epost as EpostService;
use \App\modules\CreditRequest\Model\Tcpdf\Epost as EpostPdf;

//blocks
use \App\modules\CreditRequest\Block\Mail\CoApplicantRequest as BlockMailCoApplicantRequest;
use \App\modules\CreditRequest\Block\Mail\CoApplicantRequestManual as BlockMailCoApplicantRequestManual;

use \App\modules\CreditRequest\Block\Sms\CoApplicantRequest as BlockSmsCoApplicantRequest;
use \App\modules\CreditRequest\Block\Epost\CoApplicantRequest as BlockEpostCoApplicantRequest;
use App\Http\ArraysClass;
use Log;
class CoApplicantDataRequest extends BaseModel {
    use Singleton;

    public $enabled = false;
    public $inactivityTimeout = '7 day';
	public $mailservice;

    public function __construct($manual=null)
    {
		if(null!==($manual && $manual['manual'] ==1)){
			$this->mailservice='\App\modules\CreditRequest\Block\Mail\CoApplicantRequestManual';
			Log::info('Coapplicant mail manual: '.date("Y-m-d H:i:s").print_r($manual,true));
		}
		else{
		$this->mailservice='\App\modules\CreditRequest\Block\Mail\CoApplicantRequest';
		Log::info('Coapplicant mail manual1: '.date("Y-m-d H:i:s").print_r($manual,true));
		}
        $this->init();
    }

    public function init()
    {
        $config = Registry::getInstance()->getConfig();

        $config = new ArraysClass();
        $config=$config->conf;
        $this->enabled = $config['notification']['coApplicantRequest']['enabled'];
        $this->inactivityTimeout = $config['notification']['coApplicantRequest']['inactivityTimeout'];

        if(!$this->enabled) return;
    }

    public function addCreditRequestNote($text, $creditRequest) {
        return CreditRequestService::getInstance()->addCreditRequestNote($text, $creditRequest);
    }

    public function process() {
        if(!$this->enabled) return;

        //send email to client
		Log::info('second point: '.date("Y-m-d H:i:s"));
		$this->setCoApplicantNotificationDateSending(); 
        $this->processMailSending();

        //send mail via sftp mail server
        $this->processEpostSending();

        //send sms to client
        $this->processSmsSending();

        
    }
	

    public function setCoApplicantNotificationDateSending() {
        $creditRequest = $this->getCreditRequest();
        $creditRequest->setData(['notification_coapplicant_data_request' => date('Y-m-d H:i:s')]);
	$creditRequestNorm=\App\modules\CreditRequest\Model\Service::getInstance();
	$creditRequest=$creditRequestNorm->normoliseEncoding($creditRequest);
	//dd('$creditRequest',$creditRequest);
        CollectionCreditRequest::getInstance()->_save($creditRequest);
    }

    public function processMailSending($manualMode = false) {
		
        $creditRequest = $this->getCreditRequest();
        $client = CollectionClient::getInstance()->load($creditRequest->getKid());

        $mailService = CreditRequestMailService::getInstance();

        $mailBlock = new $this->mailservice([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => new BaseModel([
                'email' => $creditRequest->getEmail(),
            ])
        ]); 

        if($manualMode) $mailBlock->setManualMode();

        $mailSendResult = $mailService->send($mailBlock);

        if($mailSendResult) {
            //add credit request note about sent email
            $this->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $creditRequest->getEmail(), $mailBlock->creditRequestNote),
                $creditRequest
            );
            return [
                'is_success' => true,
                'message' => sprintf('Email wurde an: %s gesendet!', $creditRequest->getEmail())
            ];
        } else {
            //add credit request note about error
            $errorMessage = $mailService->getErrorMessage();
            $this->addCreditRequestNote($errorMessage, $creditRequest);
            return [
                'is_success' => false,
                'message' => $errorMessage
            ];
        }
    }

    public function processEpostSending($manualMode = false) {
        $creditRequest = $this->getCreditRequest();
        $client = CollectionClient::getInstance()->load($creditRequest->getKid());

        $epostService = EpostService::getInstance();

        $epostBlock = new BlockEpostCoApplicantRequest([
            'creditRequest' => $creditRequest,
            'sender' => $epostService->getConfig()->getSender(),
        ]);

        if($manualMode) $epostBlock->setManualMode();

        $epostSendResult = $epostService->send($epostBlock, EpostPdf::class);
		$testmodesEpost=\App\Testmode::where('service','epost')->first();
		if($testmodesEpost->testmode==1){
			   $this->addCreditRequestNote(
                sprintf('Epost gesendet: "%s"', 'TEST MODE EPOST'),
                $creditRequest
            );
		}
        elseif($epostSendResult) {
            //add credit request note about sent sms
            $this->addCreditRequestNote(
                sprintf('Epost gesendet: "%s"', $epostBlock->creditRequestNote),
                $creditRequest
            );
            return [
                'is_success' => true,
                'message' => 'Epost wurde gesendet!'
            ];
        } else {
            //add credit request note about error
            $errorMessage = $epostService->getErrorMessage();
            $this->addCreditRequestNote($errorMessage, $creditRequest);
            return [
                'is_success' => false,
                'message' => sprintf('Epost konnte nicht versendet werden!<br />%s', $errorMessage)
            ];
        }
    }

    public function processSmsSending($manualMode = false) {
        $creditRequest = $this->getCreditRequest();
        $client = CollectionClient::getInstance()->load($creditRequest->getKid());

        $clientPhone = $creditRequest->getHandyv() . $creditRequest->getHandy();
        if(empty(trim($clientPhone))) {
            $clientPhone = $creditRequest->getTelefonv() . $creditRequest->getTelefon();
        }

        $smsService = CreditRequestSmsService::getInstance();

        $smsBlock = new BlockSmsCoApplicantRequest([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'phone' => $clientPhone,
        ]);

        $smsSendResult = $smsService->send($smsBlock);
		$testmodesSMS=\App\Testmode::where('service','sms')->first();
		if($testmodesSMS->testmode==1){
			   $this->addCreditRequestNote(
                sprintf('SMS gesendet: "%s"', 'TEST MODE SMS'),
                $creditRequest
            );
		}
        elseif($smsSendResult) {
            //add credit request note about sent sms
            $this->addCreditRequestNote(
                sprintf('SMS an "%s" gesendet: "%s"', $smsBlock->getPhone(), $smsBlock->creditRequestNote),
                $creditRequest
            );
            return [
                'is_success' => true,
                'message' => sprintf('SMS wurde an: "%s" gesendet!', $smsBlock->getPhone())
            ];
        } else {
            //add credit request note about error
            $errorMessage = $smsService->getErrorMessage();
            $this->addCreditRequestNote($errorMessage, $creditRequest);
            return [
                'is_success' => false,
                'message' => sprintf('SMS konnte nicht versendet werden!<br />%s', $errorMessage)
            ];
        }
    }
	
	


    public function creditRequestStatusChange() {
        //interval
        $validFromDate = new \DateTime();
        $validFromDate->sub(\DateInterval::createFromDateString($this->inactivityTimeout));

        $filter = [
            'status_intern' => \App\modules\CreditRequest\Model\CreditRequest::STATUS_WDV_MA,
            'notification_coapplicant_data_request' => ['<=' => $validFromDate->format('Y-m-d H:i:s'),'!=' => '1970-01-01 00:00:00'],
			
        ];
        $entitiesCreditRequestList = CollectionCreditRequest::getInstance()->getList(['filter' => $filter]);

        if(!count($entitiesCreditRequestList->toArray())){
            return;

        }  //nothing to process

        foreach($entitiesCreditRequestList as $entitiyCreditRequest) {
foreach($entitiyCreditRequest as $key=>$value){
	$entitiyCreditRequest->$key=mb_convert_encoding(trim($value), 'UTF-8', mb_detect_encoding(trim($value), 'UTF-8, ISO-8859-1', true));
	if($entitiyCreditRequest->sv_pkv_datum == '' ){
		$entitiyCreditRequest->sv_pkv_datum=date('Y-m-d', strtotime(0000-00-00));
	}
	if($key=='gesamtbetrachtung' && $value==""){
				$value=0;
				$entitiyCreditRequest->$key=$value;
	}
	if($key=='anr1' && $value==""){
		$entitiyCreditRequest->$key=null;
	}
	if($key=='auxmoney_id' && $value=="" || $value==null){
		$entitiyCreditRequest->$key=null;
	}
			
}
            $entitiyCreditRequest->setData(['status_intern' => \App\modules\CreditRequest\Model\CreditRequest::STATUS_NV_KEIN_KONTAKT]);
            CollectionCreditRequest::getInstance()->_save($entitiyCreditRequest);
			
            //add credit request note
            $statuses = \App\modules\CreditRequest\Model\CreditRequest::getStatusForSelect();
            $this->addCreditRequestNote(
                sprintf('Status ge&auml;ndert: %s -> %s', $statuses[\App\modules\CreditRequest\Model\CreditRequest::STATUS_WDV_MA], $statuses[$entitiyCreditRequest->status_intern]),
                $entitiyCreditRequest
            );
        }

    }

}