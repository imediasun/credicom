<?php
    use \CreditRequest\Block\Reply\Base as BaseBlockReply;   
?>
<p>
    <?php if($availableRecipients == BaseBlockReply::AVAILABLE_RECIPIENTS_APPLICANT) {?>
        Sehr <?= ($creditRequest->getAnr() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname() ?>,
    <?php } ?>

    <?php if($availableRecipients == BaseBlockReply::AVAILABLE_RECIPIENTS_COAPPLICANT) {?>
        Sehr <?= ($creditRequest->getAnr1() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname1() ?>,
    <?php } ?>

    <?php if($availableRecipients == BaseBlockReply::AVAILABLE_RECIPIENTS_BOTH) {?>
        Sehr <?= ($creditRequest->getAnr() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname() ?>,
        <?php if($creditRequest->getMasteller()) { ?>
            sehr <?= ($creditRequest->getAnr1() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname1() ?>,
        <?php } ?>
    <?php } ?>
</p>	


