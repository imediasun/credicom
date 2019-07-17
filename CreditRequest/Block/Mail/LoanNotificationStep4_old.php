<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep4_old extends BaseNotification {
    public $template = 'emails/mail/loan/loanNotificationStep4';
    public $mailSubject = 'Ihr Kreditangebot – letzte Erinnerung';
    public $creditRequestNote = 'Step 4';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}