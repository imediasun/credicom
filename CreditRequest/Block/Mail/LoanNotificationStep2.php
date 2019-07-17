<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep2 extends BaseNotification {
    public $template = 'emails/mail/loan/loanNotificationStep2';
    public $mailSubject = 'Ihre Kreditauzahlung';
    public $creditRequestNote = 'Step 2';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}