<?php

namespace App\modules\Auxmoney\Model\Service;

//traits
use App\AuxmoneyReply;
use App\Http\ArraysClass;
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Auxmoney\Model\Auxmoney as AuxmoneyModel;
use \App\modules\Auxmoney\Model\Client\Rest as AuxmoneyRestAPI;

use \App\modules\Auxmoney\Model\Service\Mapper\Request as AuxmoneyRequestMapper;
use \App\modules\Auxmoney\Model\Service\Mapper\Response as AuxmoneyResponseMapper;

use \App\modules\CreditRequest\Collection\CreditRequest as CreditRequestCollection;
use \App\modules\Auxmoney\Collection\Auxmoney as AuxmoneyCollection;
use App\CreditOrder;

class Auxmoney extends BaseModel {
    use Singleton;    
    
    public $enabled = false;
    public $testMode = false;
    public $localTestResponse = true;
    public $urlRestApi;
    public $urlKey;
    public $emailForPositiveAnswer; 

    public function __construct() {
        $this->init();
    }

    public function init() {

        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->enabled = $config['api']['auxmoney']['enabled'];
         if(!$this->enabled) return;
        $this->testMode = $config['api']['auxmoney']['testMode']['enabled'];
        $this->localTestResponse = $config['api']['auxmoney']['testMode']['localTestResponse'];

        $this->urlRestApi = ($this->testMode) ? $config['api']['auxmoney']['testMode']['urlRestApi'] : $config['api']['auxmoney']['urlRestApi'];
        $this->urlKey = $config['api']['auxmoney']['urlKey'];
        $this->emailForPositiveAnswer = ($this->testMode) ? $config['api']['auxmoney']['testMode']['emailForPositiveAnswer'] : null ;
    }


    public function getAuxmoneyIdByCreditRequestData($isMainApplicant, $creditRequestId, $creditRequestCode = null) {
        if(!$creditRequestCode) {
            $queryParams = $creditRequestId;
        } else {
            $queryParams = [
                'filter' => [
                    'id' => $creditRequestId,
                    'code' => $creditRequestCode
                ]
            ];
        }
        $entityCreditRequest = CreditRequestCollection::getInstance()->load($queryParams);

        if(!$entityCreditRequest) return false;

        $auxmoneyId = (boolval($isMainApplicant)) ? $entityCreditRequest['auxmoney_id'] : $entityCreditRequest['coapplicant_auxmoney_id'];
        if(!$auxmoneyId) return false;

        return $auxmoneyId;
    }
    
    public function getContractLinkUrl($auxmoneyId) {
        $url = false;
        $auxmoneyCollection = AuxmoneyCollection::getInstance();
        $entityAuxmoney = $auxmoneyCollection->load($auxmoneyId);
        
        if(!empty($entityAuxmoney['contract'])) {
            $entityCreditRequest = CreditRequestCollection::getInstance()->load($entityAuxmoney['creditRequestId']); 
            $code = $entityCreditRequest['code'];
            $creditRequestId = $entityAuxmoney['creditRequestId'];
            $isMainApplicant = $entityAuxmoney['mainApplicant'];
            
            $url = /* $GLOBALS['file_root'] .  */"/auxmoney/contract/view/$isMainApplicant/$creditRequestId/$code";
        }
       // dump('getContractLinkUrl()',$url);
        return $url;
    }
    
    public function saveContract($contract, $auxmoneyId) { 
	
        $auxmoneyCollection = AuxmoneyCollection::getInstance();
        $entityAuxmoney = $auxmoneyCollection->load($auxmoneyId);
        //$entityAuxmoney =AuxmoneyReply::where('id',$auxmoneyId)->first();
        $fileName = $this->getContractFileName($entityAuxmoney['creditRequestId'], $entityAuxmoney['creditId']);
        $contractFileRelativePath = $this->saveContractToFile($contract, $fileName);
        
        $entityAuxmoney->setData([
            'contract' => ($contractFileRelativePath) ? $contractFileRelativePath : '',
        ]);
       // $entityAuxmoney->save();
       $auxmoneyCollection->_save($entityAuxmoney);
    } 
    
    public function getContractFileName($creditRequestId, $auxmoneyCreditId) {
        return $creditRequestId . '-' . $auxmoneyCreditId . '.pdf';
    }
    
    public function saveContractToFile($contract, $fileName) {
        if(!$contract) return false;
        
        $contractFile = AuxmoneyModel::CONTRACTS_DIR_RELATIVE_PATH . $fileName;
        $path = base_path();
        $result = file_put_contents( $path.$contractFile, base64_decode($contract));
        if(!$result) return false;
        
        return $contractFile;
    }        
    
    public function getResponse($response) {
        //dump($response);
        $entityCreditRequest = $this->getCreditRequest();
		
		$iban=$entityCreditRequest['iban'];	
		if(empty($iban)) {
            $iban='DE19500207000001234567';
        }	
		$entityCreditRequest['iban']=$iban;   
		
        $isMainApplicant = $this->getIsMainApplicant();
		//var_dump($response);
        $replyType = AuxmoneyResponseMapper::getInstance()->processResponse($response, $entityCreditRequest, $isMainApplicant);
		
		
		//временно - нужно будет вернуть//
        //$isMainApplicant = $this->getIsMainApplicant();
        return $replyType;
    }
    
    public function sendRequest($additionalData = null) {
        //dump('sendRequest 131');

        $entityCreditRequest = $this->getCreditRequest();
        $isMainApplicant = $this->getIsMainApplicant();
        if($isMainApplicant) {
            $requestData = AuxmoneyRequestMapper::getInstance()->prepearingApplicantRequestData($entityCreditRequest, $additionalData);
 
        } else {
            $requestData = AuxmoneyRequestMapper::getInstance()->prepearingCoApplicantRequestData($entityCreditRequest, $additionalData);
        }

        $restAPI = new AuxmoneyRestAPI([
            'url' => $this->urlRestApi,
            'urlKey' => $this->urlKey
        ]);
        //Simulation of positive answers on the test system
        if($this->emailForPositiveAnswer) {
            $requestData['contact_data']['email'] = $this->emailForPositiveAnswer;
        }
        //dump($this->localTestResponse);
        if($this->localTestResponse && $this->localTestResponse['enable']) {
        $response = file_get_contents(APP_ROOT . "/app/modules/Auxmoney/TestData/response/{$this->localTestResponse['response_type']}.txt");
//echo "<hr><hr>DATA FOR AUXMONEY REQUEST:<hr><pre>";
//var_dump($requestData);
//echo "</pre><hr>";
           // echo "<hr><hr>DATA FOR AUXMONEY REQUEST:<hr><pre>";
//var_dump($requestData);
//echo "</pre><hr>";
//        
//echo "<hr><hr>AUXMONEY RESPONSE:<hr><pre>";
//var_dump(json_decode($response));
//echo "</pre><hr>";
            return json_decode($response);
        } 
        $response = $restAPI->execute($requestData);
      // dd($response);
//var_dump($restAPI->testParam());
//
//echo "<hr><hr>DATA FOR AUXMONEY REQUEST:<hr><pre>";
//var_dump($requestData);
//echo "</pre><hr>";
//        
//echo "<hr><hr>AUXMONEY RESPONSE:<hr><pre>";
//var_dump(json_decode($response));
//echo "</pre><hr>";
        
        return json_decode($response);        
    }    
 
}


