<?php

namespace App\modules\Sigma\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class ExaminationRequest  { //extends BaseNotification
    public $template = 'emails/mail/Sigma/examinationRequest';
    public $mailSubject = 'Schnellanfrage';


    public function render(){
        return view($this->template);
    }
}