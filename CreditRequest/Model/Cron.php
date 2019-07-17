<?php
namespace App\modules\CreditRequest\Model;

use \App\modules\Core\Model\Traits\Singleton;
use \App\modules\CreditRequest\Model\Service\StepperLoanNotification as ServiceStepperLoanNotification;
use \App\modules\CreditRequest\Model\Service\CoApplicantDataRequest as ServiceCoApplicantDataRequest;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use Log;
class Cron {
    use Singleton;

    public function processStepperLoanNotification() {
        ServiceStepperLoanNotification::getInstance()->process();
    }
    
    public function processCoApplicantCreditRequestStatusChange() {  
        ServiceCoApplicantDataRequest::getInstance()->creditRequestStatusChange();        
    }    
	
	public function processcoApplicantDataRequest() {  
        	 $filter = [
            'status_intern' => \App\modules\CreditRequest\Model\CreditRequest::STATUS_WDV_MA,
            'notification_coapplicant_data_request' => ['<=' => '1970-01-01 00:00:00']
			];

			Log::info('Work of Credicom crone Filter: '.date("Y-m-d H:i:s").
			'message'.print_r($filter,true)
			
			); 
			$entitiesCreditRequestList = CollectionCreditRequest::getInstance()->getList(['filter' => $filter]); 
			
			/* $entitiesCreditRequestList=\App\CreditOrder::where('status_intern',\App\modules\CreditRequest\Model\CreditRequest::STATUS_WDV_MA)
			->where('notification_coapplicant_data_request','1970-01-01 00:00:00')->get(); */
			
			Log::info('Work of Credicom crone Array: '.date("Y-m-d H:i:s").
			'message'.print_r($entitiesCreditRequestList,true)
			
			); 
			$service = ServiceCoApplicantDataRequest::getInstance();
			foreach($entitiesCreditRequestList as $creditRequest){
			$service->setCreditRequest($creditRequest)->process();
			}       
    }    
}