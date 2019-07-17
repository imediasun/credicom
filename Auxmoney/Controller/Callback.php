<?php
namespace App\modules\Auxmoney\Controller;

use \App\modules\Core\Controller\Base as BaseController;

use \App\modules\Core\Model\Registry;
use \App\modules\Auxmoney\Model\Service\Auxmoney as AuxmoneyService;
use \App\Http\ArraysClass;
use URL;
use App\Mail\OrderDebug;
use Mail;
class Callback extends BaseController {

    public $httpAuthUsername;
    public $httpAuthPassword;

    const AUXMONEY_RECIVER_ERRORS = [
        1 => 'Authorization failed',
        2 => 'Could not decode JSON',        
        3 => 'progress is missing'
    ]; 
    
    public function __construct() {
        $this->init();
    }

    public function init() {
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->httpAuthUsername = $config['api']['auxmoney']['httpAuthUsername'];
        $this->httpAuthPassword = $config['api']['auxmoney']['httpAuthPassword'];         
    }
    
    public function httpAuth() { 
        if(!isset($_SERVER['PHP_AUTH_USER'])) $_SERVER['PHP_AUTH_USER'] = '';
        if(!isset($_SERVER['PHP_AUTH_PW'])) $_SERVER['PHP_AUTH_PW'] = '';

        if (($_SERVER['PHP_AUTH_USER'] != $this->httpAuthUsername || $_SERVER['PHP_AUTH_PW'] != $this->httpAuthPassword )) {
            header('WWW-Authenticate: Basic realm="realm"');
            header('HTTP/1.0 401 Unauthorized');
            return false;            
        } else {
            return true;
        }  
    }    

    public function getTextForReply($data) { 
        $code = 0; 
        $reply = '';
        
        if($data === false) {
            $code = 1;
        } elseif(!$data) {
            $code = 2;
        } elseif(!isset($data->progress) || empty($data->progress)) {
            $code = 3; 
        }
        
        if($code) {
            $reply = '{
                "success":false,
                "errors":[
                    {
                        "code": ' . $code . ',
                        "description": "' . self::AUXMONEY_RECIVER_ERRORS[$code] . '"
                    }
                ]
            }';
        } else {
            $reply = '{
                "success": true,
                "errors": []
            }';
        }
        
        return $reply;
    }
    
    
    // http://dev.credicom.de/auxmoney/callback/receiver
    // http://credicom.of/auxmoney/callback/receiver
    public function receiverAction() { 
        $auth = $this->httpAuth();

        if(!$auth) {
            echo $this->getTextForReply(false);
            exit;
        } 
        
	$data = json_decode(file_get_contents("php://input"));
	//Отправляем переменную на почту $data
	// $data = json_decode($this->setTestReceiverData());
	Mail::to('imediasun@gmail.com')->send(new OrderDebug($data));
	Mail::to('imediasun@gmail.com')->send(new OrderDebug($this->getTextForReply($data))); 
        echo $this->getTextForReply($data);
        
        $auxmoneyPushModel = new \App\modules\Auxmoney\Model\Client\Push();
		
		
        $auxmoneyPushModel->processGettingPushData($data);

        exit;
    }
 
// http://dev.credicom.de/auxmoney/callback/test-create-credit-request/0/238325
    public function testCreateCreditRequestAction($isMainApplicant, $id) {   
        $auth = $this->httpAuth();

        $isMainApplicant = (boolean)$isMainApplicant;        
        $entityCreditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($id);
        $auxmoneyService = AuxmoneyService::getInstance()->setCreditRequest($entityCreditRequest)->setIsMainApplicant($isMainApplicant);
        $response = $auxmoneyService->sendRequest(); 
      
        echo "<hr>testCreateCreditRequestAction response: <br>";
        var_dump($response); 
        echo "<br>";
    }  
    
	
	
	 public function setTestReceiverData() {
        return '{
            "creditId": 13797072,
            "externalId": "238642",
            "status": 2,
            "progress": "credit_contract_generated_rkv_3",
            "description": "Kreditprojekt Storniert",
            "cancelSource": "customer",
            "loan": "19500",
            "price": "12.50",
            "eff_price": "14.70",
            "installment": 442.63,
            "duration": "60",
            "timestamp": "2016-09-29T15:00:34+0200",
            "creditDocument": "'. file_get_contents(base_path().('/app/modules/Auxmoney/TestData/contractExample.txt')).'"
        }';
    }
	
  /*   public function setTestReceiverData() {
        return '{
            "creditId": 11746978,
            "externalId": "7419ca",
            "status": 2,
            "progress": "credit_contract_generated_rkv_3",
            "description": "Kreditprojekt Storniert",
            "cancelSource": "customer",
            "loan": "19500",
            "price": "12.50",
            "eff_price": "14.70",
            "installment": 442.63,
            "duration": "60",
            "timestamp": "2016-09-29T15:00:34+0200",
            "creditDocument": "'. file_get_contents(base_path().('/app/modules/Auxmoney/TestData/contractExample.txt')).'"
        }';
    } */
    
}




