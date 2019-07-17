<?php
    use \CreditRequest\Block\Epost\Base as BaseEpostNotification;
?>

<span style="color:#808080;font-size:8pt;">credicom GmbH - Uhlandstra&szlig;e 20-25 - 10623 Berlin</span>
<br /><br />

<br /><br />
<br /><br />
<?php if($availableRecipients == BaseEpostNotification::AVAILABLE_RECIPIENTS_APPLICANT) {?>
    <?= $creditRequest->getVorname() ?> <?=  $creditRequest->getNachname() ?><br />
    <?= $creditRequest->getStr() ?> <?= $creditRequest->getStrNr() ?><br />
    <?= $creditRequest->getPlz() ?> <?= $creditRequest->getOrt() ?><br /><br />
<?php } ?>

<?php if($availableRecipients == BaseEpostNotification::AVAILABLE_RECIPIENTS_COAPPLICANT) {?>
    <?php
        $useCoapplicantAddress = (
            $creditRequest->getStr1() != '' &&
            $creditRequest->getStrNr1() != '' &&
            $creditRequest->getPlz1() != '' &&
            $creditRequest->getOrt1() != ''
        );
    ?>

    <?= $creditRequest->getVorname1() ?> <?=  $creditRequest->getNachname1() ?><br />
    <?php if($useCoapplicantAddress) {?>
        <?= $creditRequest->getStr1() ?> <?= $creditRequest->getStrNr1() ?><br />
        <?= $creditRequest->getPlz1() ?> <?= $creditRequest->getOrt1() ?><br /><br />
    <?php } else {?>
        <?= $creditRequest->getStr() ?> <?= $creditRequest->getStrNr() ?><br />
        <?= $creditRequest->getPlz() ?> <?= $creditRequest->getOrt() ?><br /><br />
    <?php }?>

<?php } ?>

<?php if($availableRecipients == BaseEpostNotification::AVAILABLE_RECIPIENTS_BOTH) {?>
    <?= $creditRequest->getVorname() ?> <?=  $creditRequest->getNachname() ?><br />
    <?= $creditRequest->getVorname1() ?> <?=  $creditRequest->getNachname1() ?><br />
    <?= $creditRequest->getStr() ?> <?= $creditRequest->getStrNr() ?><br />
    <?= $creditRequest->getPlz() ?> <?= $creditRequest->getOrt() ?><br /><br />
<?php } ?>

<br /><br /><br />
<div style="text-align:right">Berlin, <?= date("d.m.Y") ?></div><br /><br />
