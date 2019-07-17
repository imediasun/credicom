<?php

namespace App\modules\CreditRequest\Block\Reply\MainApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Yellow extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/yellowType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;

    public function __construct($array=array())
    {
        $this->auxmoney=$array['auxmoney'];
    }
}

