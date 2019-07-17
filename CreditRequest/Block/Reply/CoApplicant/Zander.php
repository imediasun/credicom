<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Zander extends BaseReplyBlock {
    public $template = 'reply/coApplicant/zander';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
}

