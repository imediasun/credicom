<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep2 extends BaseNotification {
    public $template = 'sms/loanNotificationStep2';
    public $creditRequestNote = 'Step 2';

     public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 3]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList[3]);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[3]['text']]);
    }
}