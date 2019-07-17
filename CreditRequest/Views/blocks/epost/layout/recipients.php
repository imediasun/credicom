<?php
    use \CreditRequest\Block\Epost\Base as BaseEpostNotification;
?>

<?php if($availableRecipients == BaseEpostNotification::AVAILABLE_RECIPIENTS_APPLICANT) {?>
    Sehr <?= ($creditRequest->getAnr() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname() ?>,
<?php } ?>

<?php if($availableRecipients == BaseEpostNotification::AVAILABLE_RECIPIENTS_COAPPLICANT) {?>
    Sehr <?= ($creditRequest->getAnr1() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname1() ?>,
<?php } ?>

<?php if($availableRecipients == BaseEpostNotification::AVAILABLE_RECIPIENTS_BOTH) {?>
    Sehr <?= ($creditRequest->getAnr() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname() ?>,
    sehr <?= ($creditRequest->getAnr1() == 1) ? 'geehrter Herr' : 'geehrte Frau'  ?> <?= $creditRequest->getNachname1() ?>,
<?php } ?>
<br /><br />