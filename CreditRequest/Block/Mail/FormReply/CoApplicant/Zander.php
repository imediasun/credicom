<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant;

use App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class Zander extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
    public $template = 'mail/formReply/coApplicant/zander';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "credit12.de";
}


