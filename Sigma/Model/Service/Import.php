<?php

namespace App\modules\Sigma\Model\Service;

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

//vendor
use \League\Csv\Reader as CsvReader;


class Import extends BaseModel
{
    use Singleton;

    public function process()
    {
        //get data
        $data = $this->getImportData();
        dump('$data',$data);
        if(!count($data)) {
            //TODO: log [no data to export, return]
            return false;
        }

        foreach($data as $item) {
            $this->processImportData($item);
        }

        return true;
    }

    public function getImportData()
    {
        $fileName = $this->getFile();

        $csv = CsvReader::createFromPath($fileName, 'r');
        $csv->setDelimiter(';');

        $csvKeys = ['surname', 'name', 'birthday', 'empty', 'answerA', 'answerB', 'answerC'];
        $result = [];
        dump('$csv->fetchAssoc($csvKeys)',$csv->fetchAssoc($csvKeys));
        foreach ($csv->fetchAssoc($csvKeys) as $row) {
            $item = array_map(function($value) {
//                $value = iconv("iso-8859-1", 'UTF-8', $value);
                return trim($value);
            }, $row);

            //skip empty rows
            $isEmpty = true;
            foreach ($item as $k => $v) {
                if(!empty($v)) $isEmpty = false;
            }

            if($isEmpty) continue;
            $result[] = $item;
        }

        return $result;
    }

    public function processImportData($data = [])
    {
        //save reply data
        $reply = $this->saveImportDataAsReply($data);

        //find client credit request
		
		dump('$reply',$reply);
        $creditRequest = $this->loadCreditRequestForReply($reply)['credit_request'];
		$ma=$this->loadCreditRequestForReply($reply)['ma'];
		dump('$creditRequest',$creditRequest);
        if(!$creditRequest) {
            //TODO: log: data, no creditRequest
            return;
        }
		dump('$creditRequest',$creditRequest);
        $reply->setCreditRequest($creditRequest);

        //find client
        $client = CollectionClient::getInstance()->load($creditRequest->getKid());
        if(!$client) {
            //TODO: log: data, no client found
            return;
        }
        $reply->setCreditRequestClient($client);

        //get reply type based on answers
        $replyType = $this->getReplyType($reply);

        //save reply
        $reply->setType($replyType);
		dump('$reply',$reply);
        CollectionReply::getInstance()->_save($reply);

//var_dump($replyType, $data, $client, $creditRequest); exit;
       
//$replyType = Reply::TYPE_GREEN;
//$replyType = Reply::TYPE_YELLOW;
//$replyType = Reply::TYPE_RED;
        
        $isDuplicateRequest = $this->checkDuplicateRequest($creditRequest);

        if($isDuplicateRequest) {
            $this->processDuplicateReply($reply, $replyType);
            return;
        }

        //process
		dump($replyType);
        switch($replyType) {
            case Reply::TYPE_GREEN:
                $this->processReplyGreen($reply,$ma);
                break;
            case Reply::TYPE_YELLOW:
                $this->processReplyYellow($reply,$ma);
                break;
            case Reply::TYPE_RED:
                $this->processReplyRed($reply,$ma);
                break;
            default: break;
        }
//exit('<br>'.$replyType);
    }
    

    public function saveImportDataAsReply($data = [])
    {
		//dump('saveImportDataAsReply',$data);
        $fileName = $this->getFile();

        $birthdayFormat = (strlen($data['birthday']) > 8) ? 'd.m.Y' : 'd.m.y';
        $birthday = \DateTime::createFromFormat($birthdayFormat, $data['birthday']);
        if($birthday->getTimestamp() > time()) {
            $birthday->sub(new \DateInterval('P100Y'));
        }

        $entity = new Reply([
            'date' => date('Y-m-d H:i:s'),
            'file' => basename($fileName),
            'name' => iconv("iso-8859-1", 'UTF-8', $data['name']),
			'credit_request_id'=>0,
			'type'=>"",
            'surname' => iconv("iso-8859-1", 'UTF-8', $data['surname']),
            'birthday' => $birthday->format('Y-m-d'),
            'answerA' => $data['answerA'],
            'answerB' => $data['answerB'],
            'answerC' => $data['answerC'],
        ]);
        CollectionReply::getInstance()->_save($entity);
        return $entity;
    }

    public function loadCreditRequestForReply($reply)
    {
	//dump('reply_name',$reply->getName());
        $result = CollectionCreditRequest::getInstance()->load([
            'filter' => [
                'vorname' => iconv('UTF-8', "iso-8859-1", $reply->getName()),
                'nachname' => iconv('UTF-8',"iso-8859-1" , $reply->getSurname()),
                'gebdat' => $reply->getBirthday(),
                'status_intern' => ModelCreditRequest::STATUS_WDV_SK,
            ],
            'sort' => [
                'datum' => 'ASC'
            ]
        ]);
		//dump('loadCreditRequestForReply', $result);
        if(!$result){
			
			$result = CollectionCreditRequest::getInstance()->load([
            'filter' => [
                'vorname1' => iconv('UTF-8', "iso-8859-1", $reply->getName()),
                'nachname1' => iconv('UTF-8',"iso-8859-1" , $reply->getSurname()),
                'gebdat1' => $reply->getBirthday(),
                'status_intern' => ModelCreditRequest::STATUS_WDV_SKMA,
            ],
            'sort' => [
                'datum' => 'ASC'
            ]
        ]);
		if($result){$ma=true;}
		if(!$result) return null;
		} //return null;


        //update reply
        $reply->setCreditRequestId($result->getId());
		$res['credit_request']=$result;
		$res['ma']= (isset($ma) && null!==$ma) ? $ma : false;
        return $res;
    }

    public function getReplyType($reply)
    {
		
		//dump('$reply',$reply);
        $a = $reply->getAnswerA();
        $b = $reply->getAnswerB();
        $c = $reply->getAnswerC();

        if(
            (empty($a) && empty($b) && empty($c)) ||
            (empty($a) && empty($b) && strtoupper($c) == 'A') ||
            (!empty($a) && empty($b) && strtoupper($c) == 'A')
        ) {
			dump('point green case!');
            return Reply::TYPE_GREEN;
        }

        if(
        (!empty($a) && !empty($b) && empty($c))
        ) {
			dump('point yellow case!');
            return Reply::TYPE_YELLOW;
        }

        if(
            (!empty($a) && empty($b) && empty($c)) ||
            (empty($a) && empty($b) && strtoupper($c) != 'A') ||
            (!empty($a) && empty($b) && (empty($c) || strtoupper($c) != 'A'))
        ) {
			dump('point red case!');
            return Reply::TYPE_RED;
        }
    }

    public function addCreditRequestNote($text, $creditRequest) {
        return CreditRequestService::getInstance()->addCreditRequestNote($text, $creditRequest);
    }
    
    public function checkDuplicateRequest($creditRequest) {   
        $creditRequestId = $creditRequest->getId();     
        $result = SigmaService::getInstance()->checkDuplicateRequestList([$creditRequestId]);  

        return $result[$creditRequestId];      
    }
    
    public function processDuplicateReply($reply, $replyType) {

	//dump('processDuplicateReply');
        $creditRequest = $reply->getCreditRequest();
        $client = $reply->getCreditRequestClient();

        //update status
        CreditRequestService::getInstance()->changeInternStatus(ModelCreditRequest::STATUS_WDV_KLARUNG, $creditRequest);

        //add credit request note
        $note = '';
        switch($replyType) {
            case Reply::TYPE_GREEN:
                $note = 'Sigma Bank: Positiv';
                break;
            case Reply::TYPE_YELLOW:
                $note = sprintf('Sigma Bank: %s | %s', $reply->getAnswerA(), $reply->getAnswerB());
                break;
            case Reply::TYPE_RED:
                $note = 'Sigma Bank: abgelehnt';
                break;
            default: break;
        }
        $this->addCreditRequestNote(
            $note,
            $creditRequest
        );

        //send notification email to info@credicom.de
        $notificationRecipient = new BaseModel([
            'email' => 'info@credicom.de',
        ]);
        $mailService = CreditRequestMailService::getInstance();
        $mailBlock = new BlockMailSigmaBankYellowNotification([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => $notificationRecipient
        ]);

        $mailSendResult = $mailService->send($mailBlock);
        if($mailSendResult) {
            //add credit request note about sent email
            $this->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $notificationRecipient->getEmail(), $mailBlock->creditRequestNote),
                $creditRequest
            );
        } else {
            //add credit request note about error
            $this->addCreditRequestNote($mailService->getErrorMessage(), $creditRequest);
        }

    }
    

    public function processReplyGreen($reply,$ma=false)
    {
		
		//dump('GREEN');
        $creditRequest = $reply->getCreditRequest();
        $client = $reply->getCreditRequestClient();

        //update status
        CreditRequestService::getInstance()->changeInternStatus(ModelCreditRequest::STATUS_WDV_KLARUNG, $creditRequest);

        //add credit request note from sigma
		$ma=($ma) ? $ma='CoApplicant' : '';
			
		
		
        $this->addCreditRequestNote('Sigma Bank: '.$ma.' Positiv', $creditRequest);
		
        //send email to client
        $mailService = CreditRequestMailService::getInstance();
        $mailBlock = new BlockMailSalaryCertificateRequest([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => new BaseModel([
                'email' => $creditRequest->getEmail(),
            ])
        ]);

        $mailSendResult = $mailService->send($mailBlock);
        if($mailSendResult) {
            //add credit request note about sent email
            $this->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $creditRequest->getEmail(), $mailBlock->creditRequestNote),
                $creditRequest
            );
        } else {
            //add credit request note about error
            $this->addCreditRequestNote($mailService->getErrorMessage(), $creditRequest);
        }

        //send sms to client
        $clientPhone = $creditRequest->getHandyv() . $creditRequest->getHandy();
        if(empty(trim($clientPhone))) {
            $clientPhone = $creditRequest->getTelefonv() . $creditRequest->getTelefon();
        }

        $smsService = CreditRequestSmsService::getInstance();
        $smsBlock = new BlockSmsSalaryCertificateRequest([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'phone' => $clientPhone,
        ]);

        $smsSendResult = $smsService->send($smsBlock);
        if($smsSendResult) {
            //add credit request note about sent sms
            $this->addCreditRequestNote(
                sprintf('SMS an "%s" gesendet: "%s"', $smsBlock->getPhone(), $smsBlock->creditRequestNote),
                $creditRequest
            );
        } else {
            //add credit request note about error
            $this->addCreditRequestNote($smsService->getErrorMessage(), $creditRequest);
        }

        //send mail via sftp mail server
        $epostService = EpostService::getInstance();
        $epostBlock = new BlockEpostSalaryCertificateRequest([
            'creditRequest' => $creditRequest,
            'sender' => $epostService->getConfig()->getSender(),
        ]);
        $epostSendResult = $epostService->send($epostBlock, EpostPdf::class);
        if($epostSendResult) {
            //add credit request note about sent sms
            $this->addCreditRequestNote(
                sprintf('Epost gesendet: "%s"', $epostBlock->creditRequestNote),
                $creditRequest
            );
        } else {
            //add credit request note about error
            $this->addCreditRequestNote($epostService->getErrorMessage(), $creditRequest);
        }

    }

    public function processReplyYellow($reply,$ma=false)
    {
		//dump('YELLOW');
        $creditRequest = $reply->getCreditRequest();
        $client = $reply->getCreditRequestClient();

        //update status
        CreditRequestService::getInstance()->changeInternStatus(ModelCreditRequest::STATUS_WDV_KLARUNG, $creditRequest);
		$ma=($ma) ? $ma='CoApplicant' : '';
        //add credit request note
        $this->addCreditRequestNote(
            sprintf('Sigma Bank: '.$ma.' %s | %s', $reply->getAnswerA(), $reply->getAnswerB()),
            $creditRequest
        );

        //send notification email to info@credicom.de
        $notificationRecipient = new BaseModel([
            'email' => 'info@credicom.de',//
        ]);
        $mailService = CreditRequestMailService::getInstance();
        $mailBlock = new BlockMailSigmaBankYellowNotification([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => $notificationRecipient
        ]);

        $mailSendResult = $mailService->send($mailBlock);
        if($mailSendResult) {
            //add credit request note about sent email
            $this->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $notificationRecipient->getEmail(), $mailBlock->creditRequestNote),
                $creditRequest
            );
        } else {
            //add credit request note about error
            $this->addCreditRequestNote($mailService->getErrorMessage(), $creditRequest);
        }
    }

    public function processReplyRed($reply,$ma=false)
    {
		
		//dump('RED');
        $creditRequest = $reply->getCreditRequest();

        $creditAmount = $creditRequest->getKreditbetrag();
        $status = ModelCreditRequest::STATUS_NV_SV;
        if($creditAmount < 3000) {
            $status = ModelCreditRequest::STATUS_NV_NEGATIVE_SCHUFA;
        }
		//dump($status);
        CreditRequestService::getInstance()->changeInternStatus($status, $creditRequest);
			if($status = ModelCreditRequest::STATUS_NV_SV){
				dump('status request',$creditRequest->id);
                $creditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($creditRequest->id);
                $resultPlanfinanz24 = \App\modules\CreditRequest\Model\Service\ExportDataForPartner\Planfinanz24::getInstance()->inits($creditRequest);

            }
        //add credit request note
		$ma=($ma) ? $ma='CoApplicant' : '';
        $this->addCreditRequestNote('Sigma Bank: '.$ma.' abgelehnt', $creditRequest);
    }
}