<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest
]) ?>

<br /><br />
Sie haben auf unser Kreditangebot nicht reagiert. Wir nehmen Ihre Anfrage aus der Bearbeitung und stellen Ihren Kreditvertrag bei der Bank in Storno.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>