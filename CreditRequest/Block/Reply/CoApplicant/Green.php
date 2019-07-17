<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Green extends BaseReplyBlock {
    public $template = 'reply/coApplicant/greenType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
}

