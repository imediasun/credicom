<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep1 extends BaseNotification {
    public $template = 'sms/loanNotificationStep1';
    public $creditRequestNote = 'Step 1';

    public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 2]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList[2]);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[2]['text']]);
    }
}