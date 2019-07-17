<?php

namespace CreditRequest\Model\Service\CreditRequestForm;

//traits
use \Core\Model\Traits\Singleton;

//models
use \Core\Model\Base as BaseModel;
use \Core\Model\Registry;
use \Auxmoney\Model\Auxmoney as Auxmoney;
use \Core\Model\Utils;
use \CreditRequest\Model\CreditRequest as CreditRequestModel;

//collections
use \CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \Client\Collection\Client as CollectionClient;
use \Auxmoney\Collection\Auxmoney as CollectionAuxmoney;
use \CreditRequest\Collection\CreditCard as CollectionCreditCard;

//service
use \CreditRequest\Model\Service as CreditRequestService;
use \Auxmoney\Model\Service\Auxmoney as AuxmoneyService;
use \CreditRequest\Model\Service\Mail as CreditRequestMailService;


abstract class Base extends BaseModel {
    use Singleton;
    
    public $isMainApplicant;  
    public $replyTypeConfig;
    
    const REPLY_PAGE_TYPE_GREEN = 'Green';
    const REPLY_PAGE_TYPE_GREEN_AUXMONEY = 'GreenAuxmoney';
    const REPLY_PAGE_TYPE_YELLOW = 'Yellow';
    const REPLY_PAGE_TYPE_RED = 'Red';
    const REPLY_PAGE_TYPE_DUPLICATE = 'Duplicate';
    
    const STATUS_MAPPER_FOR_REPLY_PAGE_TYPES = [
        self::REPLY_PAGE_TYPE_GREEN => CreditRequestModel::STATUS_OFFEN, // 1
        self::REPLY_PAGE_TYPE_GREEN_AUXMONEY => CreditRequestModel::STATUS_AUXMONEY_VERTRAG, 
        self::REPLY_PAGE_TYPE_YELLOW => CreditRequestModel::STATUS_AUXMONEY,
        self::REPLY_PAGE_TYPE_RED => CreditRequestModel::STATUS_NV_SONSTIGE, // 12 
        self::REPLY_PAGE_TYPE_DUPLICATE => CreditRequestModel::STATUS_DOPPLER // 31
    ]; 
        
    const MIN_INCOME = 600;
    
    const GOOD_PROFESSIONS = [
        'Angestellter' => 29, 
        'Beamter/ Pensionäer' => 31, 
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

        $entityCreditRequest = $this->getCreditRequest();
        $formEntity = $this->getFormEntity();

        $additionalData = $this->saveFormDataToAdditionalTables($formEntity);

        $entityCreditRequest = $this->setFormDataToDbEntity($formEntity, $entityCreditRequest, $additionalData);       
        CollectionCreditRequest::getInstance()->save($entityCreditRequest);
        
        if($formEntity['kreditkarte'] == 1) {
            $this->saveCreditCardDataToDB($entityCreditRequest);
        }

        $replyType = $this->getReplyType($entityCreditRequest, $additionalData);
        
//echo "<hr>replyType: $replyType<hr>";

        $reply = $this->processReply($replyType, $entityCreditRequest);
        
        return $reply;
    }
    
    public function processReply($replyType, $entityCreditRequest) {
        $result = [];
        
//$replyType = 'Green';// GreenAuxmoney | Green | Yellow | Red
        
        $auxmoneyId = ($this->isMainApplicant) ? $entityCreditRequest['auxmoney_id'] : $entityCreditRequest['coapplicantAuxmoneyId'];
        
        $entityAuxmoney = CollectionAuxmoney::getInstance()->load(['filter' => [
            'id' => $auxmoneyId
        ]]);
        
        foreach($this->replyTypeConfig[$replyType] as $action => $value) {
            if($value) {
                $result[$action] = $this->{$action}($value, $entityCreditRequest, $entityAuxmoney);
            }
        }

        $reply = $result['getReplyBlock'];
        
        return $reply;
    } 
    
    public function sendInfoNotification($mailBlockName, $entityCreditRequest, $entityAuxmoney = null) {
        
        $config = \Core\Model\Registry::getInstance()->getConfig();

        if(!$config['notification']['creditRequestForm']['enabled']) return false;
        $credicomRecipient = $config['notification']['creditRequestForm']['credicomRecipient'];
            
        $client = CollectionClient::getInstance()->load($entityCreditRequest->getKid());        

        //add credit request note
        $this->addCreditRequestNote(
            'Mitantragsteller wurde hinzugef&uuml;gt',
            $entityCreditRequest
        );

        //send notification email to info@credicom.de
        $notificationRecipient = new BaseModel([
            'email' => $credicomRecipient,
        ]);
        $mailService = CreditRequestMailService::getInstance();   
        
        $mailBlock = new $mailBlockName([
            'creditRequest' => $entityCreditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => $notificationRecipient
        ]);

        $mailSendResult = $mailService->send($mailBlock);
        if($mailSendResult) {
            //add credit request note about sent email
            $this->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $notificationRecipient->getEmail(), $mailBlock->creditRequestNote),
                $entityCreditRequest
            );
            return true;
        } else {
            //add credit request note about error
            $this->addCreditRequestNote($mailService->getErrorMessage(), $entityCreditRequest);
            return false;
        }
    }    
    
    public function getReplyBlock($replyBlockName, $entityCreditRequest, $entityAuxmoney) {

        $reply = new $replyBlockName([
            'creditRequest' => $entityCreditRequest,
            'ekf_url' => $entityAuxmoney['ekf_url'],
        ]);
        
        return $reply;
    }
    
    public function changeStatus($internalStatusId, $entityCreditRequest, $entityAuxmoney = null) { 
        CreditRequestService::getInstance()->changeInternStatus($internalStatusId, $entityCreditRequest);
        
        return true;
    }
    
    public function sendEmail($mailBlockName, $entityCreditRequest, $entityAuxmoney) {
        $client = CollectionClient::getInstance()->load($entityCreditRequest->getKid());
        
        $mailService = CreditRequestMailService::getInstance();
        
        $mailBlock = new $mailBlockName([
            'creditRequest' => $entityCreditRequest,
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => new BaseModel([
                'email' => $entityCreditRequest->getEmail(),
            ]),
            'auxmoney' => $entityAuxmoney
        ]);

        $mailSendResult = $mailService->send($mailBlock);

        if($mailSendResult) {
            //add credit request note about sent email
            $this->addCreditRequestNote(
                sprintf('Email an "%s" gesendet: "%s"', $entityCreditRequest->getEmail(), $mailBlock->creditRequestNote),
                $entityCreditRequest
            );
            return true;
        } else {
            //add credit request note about error
            $errorMessage = $mailService->getErrorMessage();
            $this->addCreditRequestNote($errorMessage, $entityCreditRequest);
            return false;
        }
    }
        
    public function getAuxmoneyReplyType($entityCreditRequest, $isMainApplicant, $additionalData = null) {
        $auxmoneyService = AuxmoneyService::getInstance()->setCreditRequest($entityCreditRequest)->setIsMainApplicant($isMainApplicant);
        
        $response = $auxmoneyService->sendRequest($additionalData);
        $replyType = $auxmoneyService->getResponse($response);
        
        return $replyType;
    }  
    
    public function getReplyType($entityCreditRequest, $additionalData = null) {        
        $replyType = '';        
        $mainApplicantAuxmoneyRequest = false;
        $coApplicantAuxmoneyRequest = false;
        $duplicate = ($additionalData && $additionalData['client']['duplicate']) ? true : false;

        $mainApplicantProfession = $entityCreditRequest['beruf'];
        $isMainApplicantVeryOld = $this->isVeryOld($entityCreditRequest['gebdat']);
        $mainApplicantNetIncome = $entityCreditRequest['netto'];
        
        $isCoApplicantEnabled = ($entityCreditRequest['masteller'] === 1) ? true : false;
        
        if($isCoApplicantEnabled) {
            $coApplicantProfession = $entityCreditRequest['beruf1'];
            $isCoApplicantVeryOld = $this->isVeryOld($entityCreditRequest['gebdat1']);
            $coApplicantNetIncome = $entityCreditRequest['netto1'];  
        }        

        if($this->isMainApplicant) {
            if(in_array($mainApplicantProfession, self::GOOD_PROFESSIONS)) {
                if($mainApplicantNetIncome > self::MIN_INCOME) {
                    $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                } else {
                    if($isCoApplicantEnabled) {
                        if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) {
                            if($coApplicantNetIncome > self::MIN_INCOME) {
                                $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                            } else { 
                                if(!$isMainApplicantVeryOld) { 
                                    $mainApplicantAuxmoneyRequest = true;
                                } 
                                if(!$isCoApplicantVeryOld) {
                                    $coApplicantAuxmoneyRequest = true;
                                } 
                                if($isMainApplicantVeryOld && $isCoApplicantVeryOld) {
                                    $replyType = self::REPLY_PAGE_TYPE_RED;
                                }
                            }  
                        } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                            if(!$isMainApplicantVeryOld) {
                                $mainApplicantAuxmoneyRequest = true;
                            } 
                            if(!$isCoApplicantVeryOld) {
                                $coApplicantAuxmoneyRequest = true;
                            } 
                            if($isMainApplicantVeryOld && $isCoApplicantVeryOld) {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
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
                }                
            } elseif (in_array($mainApplicantProfession, self::BAD_PROFESSIONS)) {
                if($isCoApplicantEnabled) {
                    if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) { 
                        if($coApplicantNetIncome > self::MIN_INCOME) {
                            $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                        } else {
                            if(!$isMainApplicantVeryOld) {
                                $mainApplicantAuxmoneyRequest = true;
                            } 
                            if(!$isCoApplicantVeryOld) {
                                $coApplicantAuxmoneyRequest = true;
                            } 
                            if($isMainApplicantVeryOld && $isCoApplicantVeryOld) {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            }
                        }  
                    } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                        if(!$isMainApplicantVeryOld) {
                            $mainApplicantAuxmoneyRequest = true;
                        } 
                        if(!$isCoApplicantVeryOld) {
                            $coApplicantAuxmoneyRequest = true;
                        } 
                        if($isMainApplicantVeryOld && $isCoApplicantVeryOld) {
                            $replyType = self::REPLY_PAGE_TYPE_RED;
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
            } else { // self::VERY_BAD_PROFESSIONS
                if($isCoApplicantEnabled) {
                    if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) {
                        if($coApplicantNetIncome > self::MIN_INCOME) {
                            $replyType = ($duplicate) ? self::REPLY_PAGE_TYPE_DUPLICATE : self::REPLY_PAGE_TYPE_GREEN;
                        } else { 
                            if($isCoApplicantVeryOld) {
                                $replyType = self::REPLY_PAGE_TYPE_RED;
                            } else {
                                $coApplicantAuxmoneyRequest = true;
                            }
                        }                          
                    } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {
                        if($isCoApplicantVeryOld) {
                            $replyType = self::REPLY_PAGE_TYPE_RED;                            
                        } else {
                            $coApplicantAuxmoneyRequest = true;
                        }                        
                    } else { // self::VERY_BAD_PROFESSIONS
                        $replyType = self::REPLY_PAGE_TYPE_RED;
                    }                     
                } else {
                    $replyType = self::REPLY_PAGE_TYPE_RED;
                }
            } 
        } else { // coApplicant            
            if(in_array($coApplicantProfession, self::GOOD_PROFESSIONS)) {
                $replyType = self::REPLY_PAGE_TYPE_GREEN; 
            } elseif (in_array($coApplicantProfession, self::BAD_PROFESSIONS)) {                
                if($isCoApplicantVeryOld) {
                    $replyType = self::REPLY_PAGE_TYPE_RED;
                } else {
                    $coApplicantAuxmoneyRequest = true;
// $mainApplicantAuxmoneyRequest = true;
                }
            } else { // self::VERY_BAD_PROFESSIONS
                $replyType = self::REPLY_PAGE_TYPE_RED;
            }
        }

        if($mainApplicantAuxmoneyRequest) {
            $replyType = $this->getAuxmoneyReplyType($entityCreditRequest, true);

            if($replyType === self::REPLY_PAGE_TYPE_RED && $coApplicantAuxmoneyRequest) {
                $replyType = $this->getAuxmoneyReplyType($entityCreditRequest, false);
            }
        } elseif($coApplicantAuxmoneyRequest) {
            $replyType = $this->getAuxmoneyReplyType($entityCreditRequest, false);
        }

        return $replyType;
    }
    
    public function getDateInterval($date1, $date2 = null) {
        $date2 = ($date2) ? $date2 : date('Y-m-d');
        
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
            
            $entityAuxmoney = CollectionAuxmoney::getInstance()->load(['filter' => [
                'code' => $code
            ]]);
            
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
        $collectionCreditCard = CollectionCreditCard::getInstance();
        $entityCreditCard = $collectionCreditCard->emptyLoad();
        
        $entityCreditCard->setData([												
            'kaid' => $entityCreditRequest['id'],
            'anr' => $entityCreditRequest['anr'],
            'vorname' => $entityCreditRequest['vorname'],
            'nachname' => $entityCreditRequest['nachname'],
            'str' => $entityCreditRequest['str'],
            'str_nr' => $entityCreditRequest['str_nr'],
            'plz' => $entityCreditRequest['plz'],
            'ort' => $entityCreditRequest['ort'],
            'land' => $entityCreditRequest['land'],
            'staat' => $entityCreditRequest['staat'],            
            'handy' => $entityCreditRequest['handy'],
            'telefon' => $entityCreditRequest['telefon'],
            'email' => $entityCreditRequest['email'],            
            'gebdat' => $entityCreditRequest['gebdat'],            
            'geb_ort' => $entityCreditRequest['geb_ort'],
            'weitere_infos' => '0',            
            'date' => $entityCreditRequest['date'],
            'status' => '1',
            'session_id' => $entityCreditRequest['session_id'],
            'ip' => $entityCreditRequest['ip'], 
            'code' => $this->generateCode(20),
        ]);
        $collectionCreditCard->save($entityCreditCard);        
    } 
    
}






