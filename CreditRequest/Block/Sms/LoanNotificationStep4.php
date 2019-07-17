<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep4 extends BaseNotification {
    public $template = 'sms/loanNotificationStep4';
    public $creditRequestNote = 'Step 4';

    public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 4]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList[4]);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[4]['text']]);
    }
}