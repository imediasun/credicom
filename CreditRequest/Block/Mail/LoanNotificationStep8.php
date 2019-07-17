<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep8 extends BaseNotification {
    public $template = 'emails/mail/loan/loanNotificationStep8';
    public $mailSubject = 'Ihre Kreditanfrage';
    public $creditRequestNote = 'Step 8';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }

}