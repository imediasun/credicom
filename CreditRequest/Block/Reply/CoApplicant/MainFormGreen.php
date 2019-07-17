<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class MainFormGreen extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/greenType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
}

