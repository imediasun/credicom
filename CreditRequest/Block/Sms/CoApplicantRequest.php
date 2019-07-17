<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class CoApplicantRequest extends BaseNotification {
    public $template = 'sms/coApplicantRequest';
    public $creditRequestNote = 'E-Mail Postfach';
	
	
	 public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 7]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[7]['text']]);
    }
}