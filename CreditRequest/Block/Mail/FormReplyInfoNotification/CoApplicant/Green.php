<?php

namespace App\modules\CreditRequest\Block\Mail\FormReplyInfoNotification\CoApplicant;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class Green extends BaseNotification {
    public $template = 'mail/formReplyInfoNotification/coApplicant/greenType';
    public $mailSubject = "Mitantragsteller hinzugefügt";
    public $creditRequestNote = "Mitantragsteller hinzugef&uuml;gt";
}

