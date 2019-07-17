<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class UnterlagenNotificationStep4 extends BaseNotification {
    public $template = 'emails/mail/unterlagen/UnterlagenNotificationStep4';
    public $mailSubject = 'Ihr Kreditangebot – Achtung Fristablauf!';
    public $creditRequestNote = 'Fehlende Unterlagen credicom - Schritt 5';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
}