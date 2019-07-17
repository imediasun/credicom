<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class SigmaBankYellowNotification extends BaseNotification {
    public $template = 'emails/mail/Sigma/sigmaBankYellowNotification';
    public $mailSubject = 'Sigma Bank Kunde GelbFall';
    public $creditRequestNote = 'Sigma Bank Kunde GelbFall';

    public function render(){
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient()]);
    }
}