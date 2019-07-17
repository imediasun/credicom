<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep1  extends BaseNotification{ //
    public $template = 'emails/mail/loan/loanNotificationStep1';
    public $mailSubject = 'Ihr Kreditangebot wurde versendet';
    public $creditRequestNote = 'Step 1';


    public function render(){
		dump($this->getAvailableRecipients());
		
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }

}