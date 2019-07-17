<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant;

use \App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class Zander extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
    public $template = 'mail/formReply/mainApplicant/zander';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "credit12.de";
    public $sender;
    public $to;
    public $client;
    public $creditRequest;

    public function __construct($array=array())
    {
        $this->to=$array['creditRequest']->email;
        $this->sender=$array['sender'];
        $this->client=$array['client'];
        $this->creditRequest=$array['creditRequest'];
		

    }
}


