<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep6 extends BaseNotification {
    public $template = 'sms/loanNotificationStep6';
    public $creditRequestNote = 'Step 6';

    public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 6]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList[6]);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[6]['text']]);
    }
}