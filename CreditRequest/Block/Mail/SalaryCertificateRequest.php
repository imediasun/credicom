<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class SalaryCertificateRequest extends BaseNotification {
    public $template = 'emails/mail/Sigma/salaryCertificateRequest';
    public $mailSubject = 'Ihre Kreditauszahlung - Gehaltsbescheinigung';
    public $creditRequestNote = 'Anforderung Ihrer Gehaltsbescheinigung';

   public function render(){
		dump($this->getAvailableRecipients());
		
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}