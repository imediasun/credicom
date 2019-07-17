<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant;

use App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class Yellow extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
    public $template = 'mail/formReply/coApplicant/yellowType';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "auxmoney Gelbfall";    


public function __construct($array=array())
{
    dump($array);
    $this->to=$array['creditRequest']->email;
    $this->sender=$array['sender'];
    $this->client=$array['client'];
    $this->creditRequest=$array['creditRequest'];
    $this->auxmoney=$array['auxmoney'];

}

}