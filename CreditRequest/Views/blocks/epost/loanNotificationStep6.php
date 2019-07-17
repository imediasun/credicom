<?= $this->render('creditRequest/blocks/epost/layout/header.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

<u><strong>Ihr pers&ouml;nliches Kreditangebot</strong></u><br /><br /><br />

<?= $this->render('creditRequest/blocks/epost/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

Sie haben auf unser Kreditangebot nicht reagiert. Wir nehmen Ihre Anfrage aus der Bearbeitung und stellen Ihren Kreditvertrag bei der Bank in Storno.

<?= $this->render( 'creditRequest/blocks/epost/layout/footer.php', [
    'sender' => $sender,
]) ?>