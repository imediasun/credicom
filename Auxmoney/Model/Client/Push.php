<?php
namespace App\modules\Auxmoney\Model\Client;

//models
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;
use \App\modules\Auxmoney\Model\Client\Rest as AuxmoneyRestAPI;

//service
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\CreditRequest\Model\Service\Mail as CreditRequestMailService;
use \App\modules\Auxmoney\Model\Service\Auxmoney as AuxmoneyService;

//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Client\Collection\Client as CollectionClient;
use App\Http\ArraysClass;
use App\CreditOrder;
//blocks
use \App\modules\Auxmoney\Block\Mail\AuxmoneyPushAPIRequestNotification as BlockMailAuxmoneyPushAPIRequestNotification;

class Push extends BaseModel {

    public $enabled = false;
    public $testMode = false;
    public $urlKey;

    public $notificationEmailEnabled = false;
    public $credicomRecipient = 'info@credicom.de';

    //PushApi
    public $urlPushApi;
    public $fakeProgressUrl;
    public $urlEndpointUrl;
    public $httpAuthUsername;
    public $httpAuthPassword;

    public function __construct() {
        $this->init();
    }

    public function init() {
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->enabled = $config['api']['auxmoney']['enabled'];
        if(!$this->enabled) return;

        $this->urlKey = $config['api']['auxmoney']['urlKey'];

        $this->notificationEmailEnabled = $config['notification']['auxmoney']['enabled'];
        $this->credicomRecipient = $config['notification']['auxmoney']['credicomRecipient'];

        $this->urlEndpointUrl = $config['api']['auxmoney']['urlEndpointUrl'];
        $this->httpAuthUsername = $config['api']['auxmoney']['httpAuthUsername'];
        $this->httpAuthPassword = $config['api']['auxmoney']['httpAuthPassword'];

        // for test
        $this->testMode = $config['api']['auxmoney']['testMode']['enabled'];
        $this->urlPushApi = $config['api']['auxmoney']['testMode']['urlPushApi'];
        $this->fakeProgressUrl = $config['api']['auxmoney']['testMode']['fakeProgressUrl'];
    }

    public function processGettingPushData($data) {
        $progressName = $data->progress;
        $creditRequestData = $this->parseAuxmoneyExternalId($data->externalId);

       $entityCreditRequest = CollectionCreditRequest::getInstance()->load($creditRequestData['creditRequestId']);
        //$entityCreditRequest = CreditOrder::where('id',$creditRequestData['creditRequestId'])->first();
        $auxmoneyService = AuxmoneyService::getInstance();
        $auxmoneyId = $auxmoneyService->getAuxmoneyIdByCreditRequestData($creditRequestData['isMainApplicant'], $creditRequestData['creditRequestId']);
        if(isset($data->creditDocument)) {
            $auxmoneyService->saveContract($data->creditDocument, $auxmoneyId);
        }

        $progressData = Auxmoney::AUXMONEY_PROGRESS_MAPPER[$progressName];

        $creditRequestService = CreditRequestService::getInstance();
        if($progressData['status']) {
            $creditRequestService->changeInternStatus($progressData['status'], $entityCreditRequest);
            //$this->changeAuxmoneyStatusInDb(); // ???
        }
        if($progressData['email']) {
            $this->sendAuxmoneyNotificationEmail($entityCreditRequest, $progressData['notification']);
        }

        if($progressData['notification']) {
            $notification = $progressData['notification'] . "(ist Mitantragsteller: " . (($creditRequestData['isMainApplicant']) ? 'nein' : 'ja') . ")";

            $contractLink = $auxmoneyService->getContractLinkUrl($auxmoneyId);
            if($contractLink) {
                $notification .= "(Vertrag: <a href='$contractLink' target='_blank'>aufrufen</a>)";
            }
            $creditRequestService->addCreditRequestNote($notification, $entityCreditRequest);
        }

    }

    public function parseAuxmoneyExternalId($externalId) {
        $data = [
            'isMainApplicant' => (strpos($externalId, Auxmoney::CO_APPLICANT_POSTFIX_FOR_EXTERNAL_ID) === false) ? true : false,
            'creditRequestId' => intval($externalId)
        ];

        return $data;
    }

    public function sendAuxmoneyNotificationEmail($creditRequest, $notification) {
        if(!$this->notificationEmailEnabled) return;
        $notification = ($notification) ? $notification : 'Der Kreditvertrag wurde erstellt';

        $client = CollectionClient::getInstance()->load($creditRequest->getKid());

        $notificationRecipient = new BaseModel([
            'email' => $this->credicomRecipient,
        ]);
        $mailService = CreditRequestMailService::getInstance();

        $mailBlock = new BlockMailAuxmoneyPushAPIRequestNotification([
            'creditRequest' => $creditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => $notificationRecipient,
            'notification' => $notification
        ]);
        $mailSendResult = $mailService->send($mailBlock);
        $creditRequestService = CreditRequestService::getInstance();
        if($mailSendResult) {
            //add credit request note about sent email
            $creditRequestService->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $notificationRecipient->getEmail(), $mailBlock->creditRequestNote),
                $creditRequest
            );
        } else {
            //add credit request note about error
            $creditRequestService->addCreditRequestNote($mailService->getErrorMessage(), $creditRequest);
        }
    }

    public function changeAuxmoneyStatusInDb() {
//        kreditanfragen.auxmoney_status
//        auxmoney_reply.status
//
//        green  -> STATUS_AUXMONEY_VERTRAG = 74		auxmoney_status='5'		//одобрен, есть контракт
//        yellow -> STATUS_AUXMONEY = 70  			auxmoney_status='1'		// одобрен, нужны доп. данные
//        red    -> STATUS_NV_SONSTIGE = 12;  		auxmoney_status='10'	//отклонено
    }

    public function setFakeProgress($auxmoneyCreditId, $progressNameList) {

        if(!$this->testMode) return false;

        foreach($progressNameList as $progressName) {
			dump($progressName);
			dump($auxmoneyCreditId);
            $url = sprintf($this->fakeProgressUrl, $auxmoneyCreditId);
            $requestData = [
                'property_name' => $progressName
            ];
            $restAPI = new AuxmoneyRestAPI([
                'url' => $url,
                'urlKey' => $this->urlKey
            ]);
			dump(1);
             $response = $restAPI->execute($requestData);
			 dump(9);

//$response = 'local test';
        }
        return $response; // return last Auxmoney response
    }


    public function triggerPushAPI($auxmoneyCreditId) {
        if(!$this->testMode) return false;

       $url = sprintf($this->urlPushApi, $auxmoneyCreditId);

//$url = 'http://credicom.of/auxmoney/callback/receiver';
        $requestData = [
            'endpoint_url' => $this->urlEndpointUrl,
            'http_auth_scheme' => 'basic',
            'http_auth_username' => $this->httpAuthUsername,
            'http_auth_password' => $this->httpAuthPassword
        ];
        $restAPI = new AuxmoneyRestAPI([
            'url' => $url,
            'urlKey' => $this->urlKey
        ]);
//var_dump($restAPI->testParam());

        $response = $restAPI->execute($requestData);
        return $response;
    }

}



