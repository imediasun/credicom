<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class UnterlagenNotificationStep2  extends BaseNotification{ //
    public $template = 'emails/mail/unterlagen/UnterlagenNotificationStep1';
    public $mailSubject = 'Erinnerung! Fehlende Unterlagen';
    public $creditRequestNote = 'Fehlende Unterlagen credicom - Schritt 3';


    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }

}