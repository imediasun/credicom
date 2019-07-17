<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class UnterlagenNotificationStep5 extends BaseNotification {
    public $template = 'emails/mail/unterlagen/UnterlagenNotificationStep5';
    public $mailSubject = 'Ihre Kreditanfrage';
    public $creditRequestNote = 'Fehlende Unterlagen credicom - Schritt 6';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }

}