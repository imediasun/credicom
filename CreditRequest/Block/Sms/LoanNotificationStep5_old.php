<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep5_old extends BaseNotification {
    public $template = 'sms/loanNotificationStep5';
    public $creditRequestNote = 'Step 5';

    public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 5]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList[5]);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[5]['text']]);
    }
}