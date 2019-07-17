<?php

namespace App\modules\CreditRequest\Block\Sms;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class SalaryCertificateRequest extends BaseNotification {
    public $template = 'sms/salaryCertificateRequest';
    public $creditRequestNote = 'Anforderung Gehaltsbescheinigun';
	
	 public function render(){
		
		$smsTextesCollection = \App\modules\CreditRequest\Collection\SmsTextes::getInstance();
		 $smsTextesReplyList = $smsTextesCollection->getList([
             'filter' => [
                'id' => ['=' => 8]
            ],
           
        ])->toArray(); 
		$name=$this->getCreditRequest()->getNachname();
		$charset=mb_detect_encoding(trim($name), 'UTF-8, ISO-8859-1', true);

dump('$charset',$charset);
if($charset=='ISO-8859-1'){
	$name = iconv('ISO-8859-1', "ISO-8859-1", trim($name)); // out ISO-8859-1//TRANSLIT
	}
	elseif($charset=='UTF-8'){
	$name = iconv('UTF-8', "ISO-8859-1", trim($name));// out ISO-8859-1//TRANSLIT	
	}
	else{
		$name = iconv('ISO-8859-1', "ISO-8859-1", trim($name));//in& out ISO-8859-1//TRANSLIT
	}
		dump('$name',$name);
		return view($this->template,['creditRequest'=>$this->getCreditRequest(),'text'=>$smsTextesReplyList[1]['text'],'name'=>$name]);
    }
}