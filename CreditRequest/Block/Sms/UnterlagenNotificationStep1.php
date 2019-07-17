<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class UnterlagenNotificationStep1 extends BaseNotification {
    public $template = 'sms/unterlagenNotificationStep1';
    public $creditRequestNote = 'Fehlende Unterlagen credicom - Schritt 1';

    public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 10]
            ],
           
        ])->toArray(); 
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[10]['text']]);
    }
}