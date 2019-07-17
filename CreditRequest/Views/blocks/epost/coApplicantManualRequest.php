<?= $this->render('creditRequest/blocks/epost/layout/header.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

<u><strong>Ihr Darlehensangebot</strong></u><br /><br /><br />

<?= $this->render('creditRequest/blocks/epost/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

f&uuml;r die weitere Bearbeitung Ihrer Kreditanfrage, wird ein zweiter Mitantragsteller f&uuml;r Ihren Kreditwunsch ben&ouml;tigt.
<br /><br />
Wir haben heute eine E-Mail an Sie versendet. In der E-Mail finden Sie einen Link unter welchem Sie die Angaben zu Ihrem Mitantragsteller vervollst&auml;ndigen k&ouml;nnen.
<br /><br />
Falls Sie keine E-Mail erhalten haben, pr&uuml;fen Sie Ihren Spam-Ordner oder rufen Sie uns in der Zeit vom 09:00 bis 18:00 Uhr unter der Rufnummer 030 – 6098 5721 zur&uuml;ck.
<br /><br />
Bitte beachten Sie, dass der Link in der E-Mail nur 7 Tage g&uuml;ltig ist und wir ohne weitere Angaben zum Mitantragsteller Ihre Anfrage nicht weiterbearbeiten k&ouml;nnen. 
<br /><br />
Sollten Sie in der Zwischenzeit die Angaben zu Ihrem Mitantragsteller vervollst&auml;ndigt haben, ignorieren Sie bitte dieses Schreiben.

<?= $this->render( 'creditRequest/blocks/epost/layout/footer.php', [
    'sender' => $sender,
]) ?>



 








