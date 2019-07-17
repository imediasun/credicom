<?php

namespace App\modules\CreditRequest\Model\Service\CreditRequestForm;

//traits
use App\AuxmoneyReply;
use App\CreditOrder;
use \App\modules\Core\Model\Traits\Singleton;
use DB;
//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;
use \App\modules\Core\Model\Utils;
use \App\modules\CreditRequest\Model\CreditRequest as CreditRequestModel;
use \App\Jobs\SendReplyMailDispatcher;
use \App\Jobs\CreditRequestSaveDispatcher;
//collections

use \App\modules\Auxmoney\Collection\Auxmoney as CollectionAuxmoney;
use \App\modules\CreditRequest\Collection\CreditCard as CollectionCreditCard;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
//service
use Log;
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\Sigma\Model\Service\SigmaCheck;
use \App\modules\Auxmoney\Model\Service\Auxmoney as AuxmoneyService;
//use \App\modules\CreditRequest\Model\Service\BaseMail as CreditRequestMailService;
use App\Client;
use \App\modules\CreditRequest\Model\Service\ExportDataForPartner\Zander as ZanderExportService;
use Illuminate\Support\Facades\Request;
use App\Jobs\SendDslAngebotRequest;

abstract class Base extends BaseModel {
    use Singleton;
    
    public $isMainApplicant;  
    public $replyTypeConfig;
    public $auxmoneyId = NULL;
    const REPLY_PAGE_TYPE_GREEN = 'Green';
    const REPLY_PAGE_TYPE_GREEN_AUXMONEY = 'GreenAuxmoney';
    const REPLY_PAGE_TYPE_YELLOW = 'Yellow';
    const REPLY_PAGE_TYPE_RED = 'Red';
	const REPLY_PAGE_TYPE_RED_AUXMONEY = 'Red';
    const REPLY_PAGE_TYPE_DUPLICATE = 'Duplicate';
    const REPLY_PAGE_TYPE_ZANDER_EXPORT = 'ZanderExport';
    
    const STATUS_MAPPER_FOR_REPLY_PAGE_TYPES = [
        self::REPLY_PAGE_TYPE_GREEN => CreditRequestModel::STATUS_OFFEN, // 1
        self::REPLY_PAGE_TYPE_GREEN_AUXMONEY => CreditRequestModel::STATUS_AUXMONEY_VERTRAG, 
        self::REPLY_PAGE_TYPE_YELLOW => CreditRequestModel::STATUS_AUXMONEY,
        self::REPLY_PAGE_TYPE_RED => CreditRequestModel::STATUS_NV_SONSTIGE, // 12 
        self::REPLY_PAGE_TYPE_DUPLICATE => CreditRequestModel::STATUS_DOPPLER, // 31
        self::REPLY_PAGE_TYPE_ZANDER_EXPORT => CreditRequestModel::STATUS_CREDIT12_DE // 78
    ]; 
        
    const MIN_INCOME = 600;
    
    const GOOD_PROFESSIONS = [
        'Angestellter' => 29,
        'Beamter/ Pensionär' => 31,
        'Arbeiter' => 6, 
        'Rentner' => 14 
    ];
   
    const BAD_PROFESSIONS = [       
        'Selbständig' => 19, 
        'Auszubildender' => 22,
        'Student / Schüler' => 32 
    ];
    
    const VERY_BAD_PROFESSIONS = [
        'Hartz IV / Arbeitslos' => 15, 
        'Hausfrau / Hausmann' => 16
    ];
    
    const PROFESSIONS_FOR_ZANDLER = [
        'Selbständig' => 19
    ];
    

    public function __construct() {
        $this->init();
    }
    
    abstract public function init();     
    abstract public function setFormDataToDbEntity($formEntity, $entityCreditRequest, $additionalData);    
    abstract public function setDataToFormEntity($formEntity, $entityCreditRequest);    
    abstract public function saveFormDataToAdditionalTables($formEntity);
    
       
    public function addCreditRequestNote($text, $creditRequest) {
        return CreditRequestService::getInstance()->addCreditRequestNote($text, $creditRequest);
    }
    
    public function processDataForm() { 
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'start process processDataForm()99string');
     $formEntity = $this->getFormEntity();
       $entityCreditRequest = $this->getCreditRequest();
        $additionalData = $this->saveFormDataToAdditionalTables($formEntity);

        $entityCreditRequest = $this->setFormDataToDbEntity($formEntity, $entityCreditRequest, $additionalData);
		if($entityCreditRequest->id==''){
			$entityCreditRequest->id=null;
		}
		//повторный вызов
		/* $creditRequestNorm=\App\modules\CreditRequest\Model\Service::getInstance();
		$entityCreditRequest=$creditRequestNorm->normoliseEncoding($entityCreditRequest); */
		
        $entityCreditRequest=CollectionCreditRequest::getInstance()->_save($entityCreditRequest);
		//$job = (new CreditRequestSaveDispatcher($entityCreditRequest))->onConnection('rabbitmq')->onQueue('credit_request_save');;
		//dispatch($job);

        
		SigmaCheck::process($entityCreditRequest['id']);
		//dispatch(new SendDslAngebotRequest($entityCreditRequest['id'])); 
		$iban=$entityCreditRequest['iban'];	
		if(empty($iban)) {
            $iban='DE19500207000001234567';
        }	
		$entityCreditRequest['iban']=$iban;
        
        if(isset($formEntity['kreditkarte']) && $formEntity['kreditkarte'] == 1) {

            $this->saveCreditCardDataToDB($entityCreditRequest);
        }
        $replyType = $this->getReplyType($entityCreditRequest, $additionalData);
		$isCoApplicant=$replyType['isCoApplicant'];
		$replyType =$replyType['replyType'];
        $reply = $this->processReply($replyType, $entityCreditRequest,$isCoApplicant);
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'finish process processDataForm() 131string');
        return $reply;
    }
    
    public function processReply($replyType, $entityCreditRequest,$isCoApplicant ) {
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'start process processReply 137string');
        $result = [];
//$replyType = 'Zander';// GreenAuxmoney | Green | Yellow | Red | Duplicate | Zander
        $auxmoneyId = (!$isCoApplicant) ? $entityCreditRequest->auxmoneyId : $entityCreditRequest->coapplicantAuxmoneyId;
        $entityAuxmoney =AuxmoneyReply::where('id',$auxmoneyId)->first();
	   if($isCoApplicant && $replyType!=='Duplicate' && $this->isMainApplicant==true){
		 $replyTypeConfig=$this->CoApplicantReplyTypeConfig;
	   }
	   else{
		  $replyTypeConfig=$this->ReplyTypeConfig; 
	   }
        foreach($replyTypeConfig[$replyType] as $action => $value) {
		    if($value /*&& $action=='getReplyBlock'*/) {
                 $result[$action] = $this->{$action}($value, $entityCreditRequest); // new edition
				
            }
			
        }
        $reply['CreditRequest'] =$entityCreditRequest;
        $reply['reply'] = $result['getReplyBlock'];

        $this->additionalProcessReply($replyType, $entityCreditRequest);
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'finish process processReply 160string');
        return $reply;
    }

    public function additionalProcessReply($replyType, $entityCreditRequest) {

        if($replyType === self::REPLY_PAGE_TYPE_GREEN) {
            $entityCreditRequest->send_bank=0;
          CollectionCreditRequest::getInstance()->_save($entityCreditRequest);
        }
    }
    
    public function sendInfoNotification($mailBlockName, $entityCreditRequest, $entityAuxmoney = null) {
		$job = (new SendReplyMailDispatcher($mailBlockName, $entityCreditRequest,$this->isMainApplicant))->onConnection('rabbitmq')->onQueue('credit_request_notification');;
		dispatch($job);

    }    
    
    public function getReplyBlock($replyBlockName, $entityCreditRequest) {
        $entityAuxmoney = $this->getEntityAuxmoney();
      $reply = new $replyBlockName([
            'creditRequest' => $entityCreditRequest,
          'auxmoney' => ($entityAuxmoney) ? $entityAuxmoney : null,
        ]);
        return $reply;
    }
    
    public function changeStatus($internalStatusId, $entityCreditRequest) {
        CreditRequestService::getInstance()->changeInternStatus($internalStatusId, $entityCreditRequest);
        return true;
    }
    
    public function sendEmail($mailBlockName, $entityCreditRequest) {
		$entityAuxmoney =$this->getEntityAuxmoney();
		$host=Request::server ("HTTP_HOST");
		$job = (new SendReplyMailDispatcher($mailBlockName, $entityCreditRequest,$entityAuxmoney,$host))->onConnection('rabbitmq')->onQueue('credit_request_mail');;
		dispatch($job);
		
    }
        
    public function getAuxmoneyReplyType($entityCreditRequest, $isMainApplicant, $additionalData = null) {
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'start process getAuxmoneyReplyType 268string');
		$iban_other='DE19500207000001234567';
		if(empty($entityCreditRequest->iban)) {
            $entityCreditRequest->iban=$iban_other;
        }	
		if(empty($entityCreditRequest->coapplicant_iban)) {
            $entityCreditRequest->coapplicant_iban=$iban_other;
        }
		
        $auxmoneyService = AuxmoneyService::getInstance()->setCreditRequest($entityCreditRequest)->setIsMainApplicant($isMainApplicant);
        $response = $auxmoneyService->sendRequest($additionalData);
        $replyType = $auxmoneyService->getResponse($response);
        $this->auxmoneyId = ($isMainApplicant) ? $entityCreditRequest->auxmoney_id : $entityCreditRequest->coapplicant_auxmoney_id;
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'finish process getAuxmoneyReplyType 282string');
        return $replyType;
    }

    public function getEntityAuxmoney() {
        $entityAuxmoney = NULL;
        if($this->auxmoneyId) {

           $entityAuxmoney =AuxmoneyReply::where('id',$this->auxmoneyId)->first();
        }
        return $entityAuxmoney;
    }
    
    public function getReplyType($entityCreditRequest, $additionalData = null) {
        $replyType = '';
        $mainApplicantAuxmoneyRequest = false;
        $coApplicantAuxmoneyRequest = false;
        $duplicate = ($additionalData && $additionalData['client']['duplicate']) ? true : false;

        $mainApplicantProfession = (int)$entityCreditRequest->beruf;
        $isMainApplicantVeryOld = $this->isVeryOld($entityCreditRequest->gebdat);
        $mainApplicantNetIncome = $entityCreditRequest->netto;
		Log::info('$basedate_dump2: '.date("Y-m-d H:i:s"));
        $isCoApplicantEnabled = ($entityCreditRequest->masteller == 1) ? true : false; //===
		
        if($isCoApplicantEnabled) {
            $coApplicantProfession = $entityCreditRequest->beruf1;
            $isCoApplicantVeryOld = $this->isVeryOld($entityCreditRequest->gebdat1);
            $coApplicantNetIncome = $entityCreditRequest->netto1;
        }
		$isCoApplicant=false;		
        if($this->isMainApplicant) {
            if(in_array($mainApplicantProfession, self::GOOD_PROFESSIONS)) {

                if($mainApplicantNetIncome > self::MIN_INCOME) {
                    $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                } else {
                    if($isCoApplicantEnabled) {
						$isCoApplicant=true;
                        if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) {
                            if($coApplicantNetIncome > self::MIN_INCOME) {
                                $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                            } else {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            }  
                        } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                            if($coApplicantNetIncome > self::MIN_INCOME) {                                
                                if($isCoApplicantVeryOld) {
                                    $replyType = self::REPLY_PAGE_TYPE_RED;
                                } else {
                                    $coApplicantAuxmoneyRequest = true;
									
                                }                                
                            } else {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            }
                        } else { // self::VERY_BAD_PROFESSIONS
                            $replyType = self::REPLY_PAGE_TYPE_RED;
                        }                        
                    } else {
                        $replyType = self::REPLY_PAGE_TYPE_RED;
                    }
                }                
            } elseif (in_array($mainApplicantProfession, self::BAD_PROFESSIONS)) {
                 if($mainApplicantNetIncome > self::MIN_INCOME) {
                    if($isCoApplicantEnabled) {
						$isCoApplicant=true;
                        if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) { 
                            if($coApplicantNetIncome > self::MIN_INCOME) {
                                $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                            } else {
                                if($isMainApplicantVeryOld) {
                                    $replyType = self::REPLY_PAGE_TYPE_RED;
                                } else {
                                    $mainApplicantAuxmoneyRequest = true;
                                }
                            }  
                        } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                            if($coApplicantNetIncome > self::MIN_INCOME) {                                
                                if(!$isMainApplicantVeryOld) {
                                    $mainApplicantAuxmoneyRequest = true;
                                } 
                                if(!$isCoApplicantVeryOld) {
                                    $coApplicantAuxmoneyRequest = true;
                                } 
                                if($isMainApplicantVeryOld && $isCoApplicantVeryOld) {
                                    $replyType = self::REPLY_PAGE_TYPE_RED;
                                }  
                            } else {
                                if($isMainApplicantVeryOld) {
                                    $replyType = self::REPLY_PAGE_TYPE_RED;
                                } else {
                                    $mainApplicantAuxmoneyRequest = true;
                                }
                            }                            
                        } else { // self::VERY_BAD_PROFESSIONS
                            if($isMainApplicantVeryOld) {

                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            } else { 
                                $mainApplicantAuxmoneyRequest = true;
                            }
                        }                        
                    } else {
                        if($isMainApplicantVeryOld) {
                            $replyType = self::REPLY_PAGE_TYPE_RED;
                        } else {
                            $mainApplicantAuxmoneyRequest = true;
                        }
                    }                 
                } else {

                    if($isCoApplicantEnabled) {
						$isCoApplicant=true;
                        if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) { 
                            if($coApplicantNetIncome > self::MIN_INCOME) {
                                $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                            } else {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            }  
                        } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                            if($coApplicantNetIncome > self::MIN_INCOME) {

                                if($isCoApplicantVeryOld) {

                                    $replyType = self::REPLY_PAGE_TYPE_RED;
                                } else {
                                    $coApplicantAuxmoneyRequest = true;
                                }                                
                            } else {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            }                        
                        } else { // self::VERY_BAD_PROFESSIONS
                            $replyType = self::REPLY_PAGE_TYPE_RED;
                        }
                    } else {
                        $replyType = self::REPLY_PAGE_TYPE_RED;
                    } 
                }
            } else { // self::VERY_BAD_PROFESSIONS
                if($isCoApplicantEnabled) {
					$isCoApplicant=true;
                    if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) {
                        if($coApplicantNetIncome > self::MIN_INCOME) {
                            $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                        } else { 
                            $replyType = self::REPLY_PAGE_TYPE_RED;
                        }                          
                    } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                        if($coApplicantNetIncome > self::MIN_INCOME) {
                            if($isCoApplicantVeryOld) {
                                $replyType = self::REPLY_PAGE_TYPE_RED;                            
                            } else {
                                $coApplicantAuxmoneyRequest = true;
                            }
                        } else { 
                            $replyType = self::REPLY_PAGE_TYPE_RED;
                        }                        
                    } else { // self::VERY_BAD_PROFESSIONS
                        $replyType = self::REPLY_PAGE_TYPE_RED;
                    }                     
                } else {
                    $replyType = self::REPLY_PAGE_TYPE_RED;
                }
            }
        } else {
			$isCoApplicant=true;		// coApplicant            
            if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) {
                $replyType = self::REPLY_PAGE_TYPE_GREEN; 
            } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {                
                if($coApplicantNetIncome > self::MIN_INCOME && !$isCoApplicantVeryOld) {
                    $coApplicantAuxmoneyRequest = true;                    
                } else {
                    $replyType = self::REPLY_PAGE_TYPE_RED;
                }
            } else { // self::VERY_BAD_PROFESSIONS
                $replyType = self::REPLY_PAGE_TYPE_RED;
            }
        }
        if($mainApplicantAuxmoneyRequest || $coApplicantAuxmoneyRequest) {
			
            $replyType = $this->sendToPartners($entityCreditRequest, $mainApplicantAuxmoneyRequest, $coApplicantAuxmoneyRequest);
        }
$_replyType['replyType']=$replyType;
$_replyType['isCoApplicant']=$isCoApplicant;
        return $_replyType;
    }
    
    public function sendToPartners($entityCreditRequest, $mainApplicantAuxmoneyRequest, $coApplicantAuxmoneyRequest) {
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'start process sendToPartners 472string');
        $replyType = false;
        $mainApplicantProfession = $entityCreditRequest->beruf;
        $isMainApplicantVeryOld = $this->isVeryOld($entityCreditRequest->gebdat);
        $mainApplicantNetIncome = $entityCreditRequest->netto;
        $coApplicantProfession = ($entityCreditRequest->masteller == 1) ? $entityCreditRequest->beruf1 : false;
        $isCoApplicantVeryOld = ($entityCreditRequest->masteller == 1) ? $this->isVeryOld($entityCreditRequest->gebdat1) : null;
        $coApplicantNetIncome = ($entityCreditRequest->masteller == 1) ? $entityCreditRequest->netto1 : false;
        if($mainApplicantAuxmoneyRequest) {

            $replyType = $this->getAuxmoneyReplyType($entityCreditRequest, true);
            if($replyType === self::REPLY_PAGE_TYPE_RED && $coApplicantAuxmoneyRequest) {
                $replyType = $this->getAuxmoneyReplyType($entityCreditRequest, false);
            }
        } else {
            $replyType = $this->getAuxmoneyReplyType($entityCreditRequest, false);
        }

        if($replyType === self::REPLY_PAGE_TYPE_RED) {
            $mainApplicantZanderExport = (in_array($mainApplicantProfession, self::PROFESSIONS_FOR_ZANDLER) && $mainApplicantNetIncome > self::MIN_INCOME && !$isMainApplicantVeryOld) ? true : false;
            $coApplicantZanderExport = (in_array($coApplicantProfession, self::PROFESSIONS_FOR_ZANDLER) && $coApplicantNetIncome > self::MIN_INCOME && !$isCoApplicantVeryOld) ? true : false;
            
            if($mainApplicantZanderExport || $coApplicantZanderExport) {
                $replyType = $this->getZanderReplyType($entityCreditRequest, $mainApplicantZanderExport, $coApplicantZanderExport);
            }
        }
		Log::info('Base.php: '.date("Y-m-d H:i:s").
			'finish process sendToPartners 499string');
        return $replyType;
    }
    
    public function getZanderReplyType($entityCreditRequest, $mainApplicantZanderExport, $coApplicantZanderExport) {
        $isMainApplicant = ($mainApplicantZanderExport) ? true : false;
        $isBoth = ($mainApplicantZanderExport && $coApplicantZanderExport) ? true : false;        
        
        ZanderExportService::getInstance()->setCreditRequest($entityCreditRequest)->setIsMainApplicant($isMainApplicant)->setIsBoth($isBoth)->process(); 
        
        $replyType = self::REPLY_PAGE_TYPE_ZANDER_EXPORT;
        return $replyType;
    }
    
    public function getDateInterval($date1, $date2 = null) {

        $date2 = (isset($date2)) ? $date2 : date('Y-m-d');
        $date1 = new \DateTime($date1);
        $date2 = new \DateTime($date2); 

        return $date1->diff($date2);
    }
    
    public function isVeryOld($birthDate) {
        $dateInterval = $this->getDateInterval($birthDate);
        if($dateInterval->y > 69) {
            return true;            
        }        
        return false;
    }
    
    public function generateCode($number){
        $is_unique_code = false;
        $code = false;

        while(!$is_unique_code) {
            $code = $this->generatePseudorandomNumber($number);
            $entityAuxmoney =AuxmoneyReply::where('code', $code)->first();
            
            if(!$entityAuxmoney) {
                $is_unique_code = true;
            }	
        }
        
        return $code;
    }
    
    public function generatePseudorandomNumber($number){
        $code = '';
        $characters = '123456789abcdefghijklmnopqrstuvwxyz';

        srand((double)microtime() * 1000000);
        
        for($i = 0; $i < $number; $i++){
            $code .= substr($characters,(rand() % (strlen($characters))), 1);
        }
        return $code;
    }
    
    public function saveCreditCardDataToDB($entityCreditRequest) {        

    } 
    
}






