<?php

namespace App\modules\Auxmoney\Model\Service\Mapper;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//service
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\Auxmoney\Model\Service\Auxmoney as AuxmoneyService;
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\Base as CreditRequestFormService;

//models
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;
use \App\AuxmoneyReply as AuxmoneyReply;
use \App\modules\Core\Model\Base as BaseModel;

//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Auxmoney\Collection\Auxmoney as CollectionAuxmoney;


class Response extends BaseModel {
    use Singleton;

    public $options; 

    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->options = \App\Http\Model\CreditForm\Options::getInstance();
    } 
    
    public function processResponse($response, $entityCreditRequest, $isMainApplicant) {
        $replyType = null;
       //var_dump('response=>',$response);
        if($response->is_success) {

            if($response->manual_quality_assurance) { //True: The customer is audited by our quality assurance. For this we need the gapless account statements of the last 3 months for all accounts of the customer.
                $replyType = Auxmoney::AUXMONEY_RESPONSE_TYPE['requires_additional_data']; // Yellow (STATUS_AUXMONEY = 70)
                $auxmoneyStatusId = 1;
                $sendEmail = 1;
            } else { //False: The customer does not need to be audited. After signing the loan can be disbursed.
                $replyType = Auxmoney::AUXMONEY_RESPONSE_TYPE['approved']; // GreenAuxmoney (STATUS_AUXMONEY_VERTRAG = 74) 
                $auxmoneyStatusId = 5;
                $sendEmail = 0; 
            }            
            $this->addAuxmoneyPositiveNote($entityCreditRequest, $response, $replyType, $isMainApplicant); 
            $this->saveAuxmoneyDataToDb($response, $entityCreditRequest, $isMainApplicant, $replyType, $auxmoneyStatusId, $sendEmail);
            
        } else { //is_success = false
		
            $note = '';
            
            if(isset($response->violations[0]->property_path)) { //has errors
                $replyType = Auxmoney::AUXMONEY_RESPONSE_TYPE['rejected_with_error']; // Red
                
                $note = 'Auxmoney API-Fehler - ';            
                foreach($response->violations as $error) {
                    $error_path=(isset($error->property_path)) ? $error->property_path : 'not_property_error';
                    $note .= "( {$error_path}: {$error->message} ) ";
                }  
                $this->addAuxmoneyNegativeNote($entityCreditRequest, $replyType, $note, $isMainApplicant);
            } else {
				
				
					
				/* if($mainApplicantProfession==19 && $isMainApplicantVeryOld==false && $isCoApplicantEnabled==false && $mainApplicantNetIncome>=601){
					var_dump('popali');
					
					$replyType = Auxmoney::AUXMONEY_RESPONSE_TYPE['rejected_with_next_partner'];
				}
				else{ */
					$replyType = Auxmoney::AUXMONEY_RESPONSE_TYPE['rejected']; // STATUS_NV_SONSTIGE = 12
                
				/* } */
				///
                $auxmoneyStatusId = 10;
                $sendEmail = 0;
                
                $note = "Auxmoney negative - (message: {$response->violations[0]->message})";
                $this->addAuxmoneyNegativeNote($entityCreditRequest, $replyType, $note, $isMainApplicant);
                
                $this->saveAuxmoneyDataToDb($response, $entityCreditRequest, $isMainApplicant, $replyType, $auxmoneyStatusId, $sendEmail);
            }            
        }
		
        return $replyType;
    } 
    
    public function addAuxmoneyNegativeNote($entityCreditRequest, $replyType, $note, $isMainApplicant) {
        $statuses = \App\modules\CreditRequest\Model\CreditRequest::getStatusForSelect();
        $internalStatusId = CreditRequestFormService::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[$replyType];
		$mainApplicantProfession = (int)$entityCreditRequest->beruf;
				$isMainApplicantVeryOld = $this->isVeryOld($entityCreditRequest->gebdat);
				$isCoApplicantEnabled = ($entityCreditRequest->masteller == 1) ? true : false;
				$mainApplicantNetIncome = $entityCreditRequest->netto;
		
		
		if((isset($_SESSION['auxmoney_manual']) && null!==$_SESSION['auxmoney_manual']) ||
		($mainApplicantProfession==19 && $isMainApplicantVeryOld==false && $isCoApplicantEnabled==false && $mainApplicantNetIncome>=601)
		 ||($statuses[$internalStatusId]=='N.V. - sonstige') 
		
		){
			CreditRequestService::getInstance()->addCreditRequestNote(


					"$note
					(ist Mitantragsteller: " . (($isMainApplicant) ? 'nein' : 'ja') . ")",
					$entityCreditRequest
				);
		}
		else{
			
					CreditRequestService::getInstance()->addCreditRequestNote(


            "Status ge&auml;ndert: {$statuses[$entityCreditRequest->statusIntern]} -> {$statuses[$internalStatusId]}: 
            $note
            (ist Mitantragsteller: " . (($isMainApplicant) ? 'nein' : 'ja') . ")",
            $entityCreditRequest
        );
				
		}
        
    }

    public function addAuxmoneyPositiveNote($entityCreditRequest, $response, $replyType, $isMainApplicant) {
		//dump('addAuxmoneyPositiveNote');
       //dd($response);
        $statuses = \App\modules\CreditRequest\Model\CreditRequest::getStatusForSelect();
        $internalStatusId = CreditRequestFormService::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[$replyType];
        $notification = "Status ge".html_entity_decode('&auml;',ENT_NOQUOTES,'UTF-8')."ndert: {$statuses[$entityCreditRequest->status_intern]} -> {$statuses[$internalStatusId]}: 
                        (Laufzeit: {$response->duration})
                        (Kreditbetrag inkl. auxmoney Betrag: {$response->loan})
                        (angefragter Kreditbetrag: {$response->loan_asked})
                        (soll Zins: {$response->rate})
                        (eff. Zins: {$response->eff_rate})
                        (KRV EUR: {$response->insurance_fee})
                        (Zinsen in EUR: {$response->interest})
                        (Vorläufige monatliche Rate in Euro: {$response->installment_amount})
                        (Rate: {$response->rate})
                        (Kunden-ID: {$response->user_id})
                        (Kredit-ID: {$response->credit_id})
                        (ist Mitantragsteller: " . (($isMainApplicant) ? 'nein' : 'ja') . ")";
        if(isset($response->contract)) {
            $notification .= "(Vertrag: <a href='/auxmoney/contract/view/" . (($isMainApplicant) ? 1 : 0) . "/{$entityCreditRequest['id']}/{$entityCreditRequest['code']}' target='_blank'>aufrufen</a>)";
        }

        CreditRequestService::getInstance()->addCreditRequestNote($notification, $entityCreditRequest);        
    }
    
    
/*    public function saveAuxmoneyDataToDb($response, $entityCreditRequest, $isMainApplicant, $replyType, $auxmoneyStatusId, $sendEmail) {
        $internalStatusId = CreditRequestFormService::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[$replyType];
        
        $contractFileRelativePath = false;
        if(isset($response->contract)) {
             $auxmoneyService = AuxmoneyService::getInstance();
            $fileName = $auxmoneyService->getContractFileName($entityCreditRequest->id, $response->credit_id);
            $contractFileRelativePath = $auxmoneyService->saveContractToFile($response->contract, $fileName);
        }
//dump($response);
        //dd($contractFileRelativePath);
        $auxmoney = new AuxmoneyReply([//new Auxmoney
            'credit_request_id' => $entityCreditRequest->id,
            'main_applicant' => ($isMainApplicant) ? 1 : 0,
            'date' => date("Y-m-d H:i:s",time()),
            'code'=> 0,
            'cron'=> 0,
            'user_id' => ((isset($response->user_id)) ? $response->user_id : 0),
            'credit_id' => ((isset($response->credit_id)) ? $response->credit_id : 0),
            'status' => $auxmoneyStatusId,
            'contract' => ($contractFileRelativePath) ? $contractFileRelativePath : '',
            'ekf_url' => ((isset($response->ekf_url)) ? $response->ekf_url : ''),
            'send_email' => $sendEmail,// 1 = true, 0 = false
            //'cron' => 0
        ]);
        //CollectionAuxmoney::getInstance()->save($auxmoney);
$auxmoney->save();
        $auxmoneyIdFieldName = ($isMainApplicant) ? 'auxmoney_id' : 'coapplicant_auxmoney_id';
        $previousStatus = $entityCreditRequest->status_intern;//getStatusIntern()

        $entityCreditRequest->setData([
            'status_intern' => $internalStatusId,
                'auxmoney_status' => $auxmoneyStatusId,                                     //for compatibility with old code
                'auxmoney_user_id' => ((isset($response->user_id)) ? $response->user_id : 0),     //for compatibility with old code
                'auxmoney_credit_id' => ((isset($response->credit_id)) ? $response->user_id : 0), //for compatibility with old code
                'auxmoney_send_email' => $sendEmail,                                        //for compatibility with old code
                'auxmoney_date' => date("Y-m-d H:i:s",time()),                              //for compatibility with old code
            $auxmoneyIdFieldName => $auxmoney['id'],
        ]);
        //CollectionCreditRequest::getInstance()->save($entityCreditRequest);
var_dump($entityCreditRequest);
        $entityCreditRequest->save();
        CreditRequestService::getInstance()->saveCreditRequestStatus($entityCreditRequest, $previousStatus);
    }*/

    public function saveAuxmoneyDataToDb($response, $entityCreditRequest, $isMainApplicant, $replyType, $auxmoneyStatusId, $sendEmail) {
        $internalStatusId = CreditRequestFormService::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[$replyType];

		////
		
		//Если профессия предприниматель + если моложе 69 лет + если без коаппликанта + доход от 601 евро
				$mainApplicantProfession = (int)$entityCreditRequest->beruf;
				$isMainApplicantVeryOld = $this->isVeryOld($entityCreditRequest->gebdat);
				$isCoApplicantEnabled = ($entityCreditRequest->masteller == 1) ? true : false;
				$mainApplicantNetIncome = $entityCreditRequest->netto;
		/////
		
		
        $contractFileRelativePath = false;
		
        if(isset($response->contract)) {
            $auxmoneyService = AuxmoneyService::getInstance();
            $fileName = $auxmoneyService->getContractFileName($entityCreditRequest['id'], $response->credit_id);
            $contractFileRelativePath = $auxmoneyService->saveContractToFile($response->contract, $fileName);
        }

        $auxmoney = new AuxmoneyReply([
            'credit_request_id' => $entityCreditRequest['id'],
            'main_applicant' => ($isMainApplicant) ? 1 : 0,
            'date' => date("Y-m-d H:i:s",time()),
            'user_id' => ((isset($response->user_id)) ? $response->user_id : 0),
            'credit_id' => ((isset($response->credit_id)) ? $response->credit_id : 0),
            'status' => $auxmoneyStatusId,
            'contract' => ($contractFileRelativePath) ? $contractFileRelativePath : '',
            'ekf_url' => ((isset($response->ekf_url)) ? $response->ekf_url : ''),
            'send_email' => $sendEmail,
            'code'=>$entityCreditRequest['code'],// 1 = true, 0 = false
            'cron' => 0
        ]);
        $auxmoney->save();
        //CollectionAuxmoney::getInstance()->save($auxmoney);

        $auxmoneyIdFieldName = ($isMainApplicant) ? 'auxmoney_id' : 'coapplicant_auxmoney_id';
        //$previousStatus = $entityCreditRequest->getStatusIntern();
        $previousStatus = $entityCreditRequest->status_intern;
	/* 	if($entityCreditRequest instanceof \App\CreditOrder){
			
		}
		else{
			$CreditRequestID=$entityCreditRequest->id;
			$entityCreditRequest=\App\CreditOrder::where('id',$CreditRequestID)->first();
		} */
       /*$entityCreditRequest->setData([
            'status_intern' => $internalStatusId,
            'auxmoney_status' => $auxmoneyStatusId,                                     //for compatibility with old code
            'auxmoney_user_id' => ((isset($response->user_id)) ? $response->user_id : 0),     //for compatibility with old code
            'auxmoney_credit_id' => ((isset($response->credit_id)) ? $response->user_id : 0), //for compatibility with old code
            'auxmoney_send_email' => $sendEmail,                                        //for compatibility with old code
            'auxmoney_date' => date("Y-m-d H:i:s",time()),                              //for compatibility with old code
            $auxmoneyIdFieldName => $auxmoney['id'],
        ]);*/
		
		////
		
		/*if(null!==($response->contract)  && $manual ){
			
		}*/
		 
		
		
		
		
         
		 
		 ////
		 $manual=(isset($_SESSION['auxmoney_manual'])) ? $_SESSION['auxmoney_manual'] : null;
		 if (!$manual ){
			$entityCreditRequest->status_intern=12; 
		 } 
		
        $entityCreditRequest->auxmoney_status=$auxmoneyStatusId;
        $entityCreditRequest->auxmoney_user_id=((isset($response->user_id)) ? $response->user_id : 0);
        $entityCreditRequest->auxmoney_credit_id=((isset($response->credit_id)) ? $response->user_id : 0);
        $entityCreditRequest->auxmoney_send_email=$sendEmail;
        $entityCreditRequest->auxmoney_date=date("Y-m-d H:i:s",time()); 
        $entityCreditRequest->$auxmoneyIdFieldName=$auxmoney['id'];
        //dd($entityCreditRequest);
		$creditRequestNorm=\App\modules\CreditRequest\Model\Service::getInstance();
		$entityCreditRequest=$creditRequestNorm->normoliseEncoding($entityCreditRequest);
     
       //   $entityCreditRequest->save();
	   if (!$manual ){
	   CreditRequestService::getInstance()->saveCreditRequestStatus($entityCreditRequest, $previousStatus);
	    CreditRequestService::getInstance()->addCreditRequestNote(
            sprintf('Status ge&auml;ndert: %s -> %s', 'Not Installed', 'N.V. - sonstige'),
            $entityCreditRequest
        );
	   }
	   
		if( (!$manual &&  ($mainApplicantProfession==19 && $isMainApplicantVeryOld==false && $isCoApplicantEnabled==false && $mainApplicantNetIncome>=601)) && ($internalStatusId!==70) && !isset($response->contract)){
			$entityCreditRequest->status_intern=12;
		}
		elseif((isset($response->contract)) || ($internalStatusId==70)){ //!$manual || ($manual && isset($response->contract))
			$entityCreditRequest->status_intern=$internalStatusId;
		}
		CollectionCreditRequest::getInstance()->_save($entityCreditRequest);
	   
	   
	   //if manual answer and negative answer from auxmoney dont save status
	   
	  
				
	   if(isset($response->contract) || ($internalStatusId==70) || (!$manual && $mainApplicantProfession!==19 && $isMainApplicantVeryOld==true && $isCoApplicantEnabled==true && $mainApplicantNetIncome<601)  ) {

		CreditRequestService::getInstance()->saveCreditRequestStatus($entityCreditRequest, $previousStatus);
	   }
    }
	
	    public function isVeryOld($birthDate) {
        $dateInterval = $this->getDateInterval($birthDate);

        //dump($dateInterval->y);
        if($dateInterval->y > 69) {
            return true;            
        }        
        return false;
    }
	
	    public function getDateInterval($date1, $date2 = null) {
        $date2 = ($date2) ? $date2 : date('Y-m-d');
        
        $date1 = new \DateTime($date1);
        $date2 = new \DateTime($date2);

        return $date1->diff($date2);
    }
    
}