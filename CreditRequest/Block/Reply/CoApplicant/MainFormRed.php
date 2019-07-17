<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class MainFormRed extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/redType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
}



