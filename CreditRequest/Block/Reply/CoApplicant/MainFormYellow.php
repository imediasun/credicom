<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class MainFormYellow extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/yellowType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;

    public function __construct($array=array())
    {
        $this->auxmoney=$array['auxmoney'];
    }
}

