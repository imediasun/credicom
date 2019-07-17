<?php

namespace App\modules\CreditRequest\Block\Reply\MainApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class GreenAuxmoney extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/greenAuxmoneyType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;

    public function __construct($array=array())
    {
        $this->auxmoney=$array['auxmoney'];
    }

}




