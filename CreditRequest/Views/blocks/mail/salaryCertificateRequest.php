<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest
]) ?>

<br /><br />
vielen Dank f&uuml;r Ihre Kreditanfrage in unserem Hause!<br /><br />
Nach Ihren gemachten Angaben steht einer Kreditauszahlung nichts im Weg.<br /><br />
Gerne unterbreiten wir Ihnen in den n&auml;chsten Tagen Ihr Kreditangebot mit Zinsen, Laufzeit und monatlicher Rate.<br />
Dringend erforderlich ist daf&uuml;r Ihre letzte Gehaltsbescheinigung, die Sie uns in Kopie per Post, E-Mail oder Fax zusenden.<br />
Originale werden von uns nicht zur&uuml;ckgeschickt! Ihren Posteingang erwarten wir innerhalb der n&auml;chsten Werktage.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>