<?php

namespace App\modules\Auxmoney\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class AuxmoneyPushAPIRequestNotification extends BaseNotification {
    public $template = 'emails/mail/auxmoneyPushAPIRequestNotification';
    public $mailSubject = 'Auxmoney - Der Kreditvertrag wurde erstellt';
    public $creditRequestNote = 'Auxmoney - Der Kreditvertrag wurde erstellt';
	
	 public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'notification'=>$this->getNotification()]);
    }
}

