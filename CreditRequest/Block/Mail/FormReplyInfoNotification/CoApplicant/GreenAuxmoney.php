<?php

namespace App\modules\CreditRequest\Block\Mail\FormReplyInfoNotification\CoApplicant;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class GreenAuxmoney extends BaseNotification {
    public $template = 'mail/formReplyInfoNotification/coApplicant/greenAuxmoneyType';
    public $mailSubject = 'auxmoney vertrag erstellt';
    public $creditRequestNote = "auxmoney vertrag erstellt";
}



