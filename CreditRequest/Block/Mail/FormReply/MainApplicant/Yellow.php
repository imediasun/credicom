<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant;

use \App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class Yellow extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
    public $template = 'mail/formReply/mainApplicant/yellowType';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "auxmoney Gelbfall";
    public $sender;
    public $to;
    public $client;
    public $creditRequest;
    public $auxmoney;

    public function __construct($array=array())
    {

        $this->to=$array['creditRequest']->email;
        $this->sender=$array['sender'];
        $this->client=$array['client'];
        $this->creditRequest=$array['creditRequest'];
        $this->auxmoney=$array['auxmoney'];

    }
}
