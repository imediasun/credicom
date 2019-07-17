<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class LoanNotificationStep5 extends BaseNotification {
    public $template = 'emails/mail/loan/loanNotificationStep5';
    public $mailSubject = 'Ihr Kreditangebot – letzte Erinnerung';
    public $creditRequestNote = 'Step 5';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}