<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep3 extends BaseNotification {
    public $template = 'emails/mail/loan/loanNotificationStep3';
    public $mailSubject = 'Ihr persönliches Kreditangebot';
    public $creditRequestNote = 'Step 3';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}