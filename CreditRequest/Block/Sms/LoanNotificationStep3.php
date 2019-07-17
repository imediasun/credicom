<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;
use \App\Sms;
class LoanNotificationStep3 extends BaseNotification {
    public $template = 'sms/loanNotificationStep3';
    public $creditRequestNote = 'Step 3';

    public function render(){
		//$text=Sms::where('id',1)->first();
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 1]
            ],
           
        ])->toArray(); 
		dump($smsTextesReplyList[1]);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[1]['text']]);
    }
}