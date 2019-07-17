<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients
]) ?>

<br /><br />
vielen Dank f&uuml;r Ihre Anfrage!
<br /><br /> 
Nach positiver Vorpr&uuml;fung wird Ihre Anfrage an unseren sehr erfolgreichen Partner weitergeleitet:
<br /><br />

FVZ GmbH
<br />
Rudolf-Walther-Str. 5
<br /> 
06188 Landsberg
<br /> 
Internet: www.credit12.de
<br /><br /> 

Sie werden umgehend von unserem Partner zur schnellen und erfolgreichen Bearbeitung Ihres Kreditwunsches kontaktiert - nat&uuml;rlich kostenfrei und unverbindlich!
  

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>

