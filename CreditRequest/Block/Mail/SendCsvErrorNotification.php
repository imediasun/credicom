<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class SendCsvErrorNotification extends BaseNotification {
    public $template = 'emails/mail/Sigma/SendCsvErrorNotification';
    public $mailSubject = 'You have an error in csv import';
    public $creditRequestNote = 'Sigma Bank Kunde GelbFall';

    public function render(){
        return view($this->template,['error'=>$this->getError(),'nachname'=>$this->getNachname(),'vorname'=>$this->getVorname()]);
    }
}