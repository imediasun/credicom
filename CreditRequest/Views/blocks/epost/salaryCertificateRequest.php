<?= $this->render('creditRequest/blocks/epost/layout/header.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

<u><strong>Ihre Darlehensanfrage</strong></u><br /><br /><br />

<?= $this->render('creditRequest/blocks/epost/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

vielen Dank f&uuml;r Ihre Kreditanfrage in unserem Hause!
<br /><br />
Nach Ihren gemachten Angaben steht einer Kreditauszahlung nichts im Weg.
<br /><br />
Gerne unterbreiten wir Ihnen in den n&auml;chsten Tagen Ihr Kreditangebot mit Zinsen, Laufzeit und monatlicher Rate.
<br /><br />
Dringend erforderlich ist daf&uuml;r Ihre letzte Gehaltsbescheinigung, die Sie uns in Kopie per Post zusenden. Originale werden von uns nicht zur&uuml;ckgeschickt! Ihren Posteingang erwarten wir innerhalb der n&auml;chsten Werktage.

<?= $this->render( 'creditRequest/blocks/epost/layout/footer.php', [
    'sender' => $sender,
]) ?>