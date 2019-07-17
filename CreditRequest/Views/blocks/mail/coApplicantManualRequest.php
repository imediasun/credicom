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
f&uuml;r die weitere Bearbeitung Ihrer Kreditanfrage, wird ein zweiter Mitantragsteller f&uuml;r Ihren Kreditwunsch ben&ouml;tigt. 
<br /><br />
Bitte vervollst&auml;ndigen Sie die Angaben zu Ihrem Mitantragsteller in unserem Formular:
<br /><br />
<a href="<?= $creditRequestUrl ?>">Kreditanfrage - <?= $creditRequest->getId() ?></a> (Der Link ist nur 7 Tage g&uuml;ltig!)
<br /><br />
Bitte beachten Sie, dass wir ohne weitere Angaben zum Mitantragsteller Ihre Anfrage nicht weiterbearbeiten k&ouml;nnen.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>
