<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep7 extends BaseNotification {
    public $template = 'emails/mail/loan/loanNotificationStep7';
    public $mailSubject = 'Ihr Kreditangebot – Achtung Fristablauf!';
    public $creditRequestNote = 'Step 7';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}