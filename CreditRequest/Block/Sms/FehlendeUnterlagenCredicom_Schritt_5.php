<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class FehlendeUnterlagenCredicom_Schritt_5 extends BaseNotification {
    public $template = 'sms/unterlagenNotificationStep4';
    public $creditRequestNote = 'Fehlende unterlagen credicom - Schritt 5';

    public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 11]
            ],
           
        ])->toArray(); 
		$search='[date + 3 days]';
		$result=date ('d.m.Y' , strtotime("+3 days"));
		$text=str_replace($search, $result, $smsTextesReplyList[11]['text']);
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$text]);
    }
}