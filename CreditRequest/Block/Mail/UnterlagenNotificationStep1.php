<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class UnterlagenNotificationStep1  extends BaseNotification{ //
    public $template = 'emails/mail/unterlagen/UnterlagenNotificationStep1';
    public $mailSubject = 'Fehlende Unterlagen';
    public $creditRequestNote = 'Fehlende Unterlagen credicom - Schritt 2';


    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }

}