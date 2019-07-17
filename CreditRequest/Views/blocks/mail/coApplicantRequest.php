<?php
    global $file_root;
    $creditRequestUrl = sprintf(
        '%scredit-request/co-applicant/form/%d/%s',
        $file_root,
        $creditRequest->getId(),
        $creditRequest->getCode()
    );
?>
    
<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest
]) ?>

<br /><br />
bei der Bearbeitung Ihrer Kreditanfrage, haben unsere Partnerbanken uns mitgeteilt, dass unbedingt ein zweiter Mitantragsteller f&uuml;r Ihren Kreditwunsch ben&ouml;tigt wird. 
<br /><br />
Bitte vervollst&auml;ndigen Sie die Angaben zu Ihrem Mitantragsteller in unserem Formular:
<br /><br />
<a href="<?= $creditRequestUrl ?>">Kreditanfrage - <?= $creditRequest->getId() ?></a> (Der Link ist nur 7 Tage g&uuml;ltig!)
<br /><br />
Sollten wir keine R&uuml;ckmeldung bis zum <strong><?= date('d.m.Y', mktime(0, 0, 0, date("m")  , date("d")+7, date("Y")));  ?></strong> von Ihnen haben, werden wir die Bearbeitung Ihrer Anfrage einstellen, da wir ohne Mitantragsteller Ihren Kreditwunsch nicht erf&uuml;llen k&ouml;nnen.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>

