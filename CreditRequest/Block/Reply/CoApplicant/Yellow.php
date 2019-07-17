<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Yellow extends BaseReplyBlock {
    public $template = 'reply/coApplicant/yellowType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
}

