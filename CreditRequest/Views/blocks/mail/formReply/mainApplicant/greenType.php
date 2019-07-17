<?php
    global $file_root;
    
    $bankLinkUrl = sprintf(
        '%sdaten-%d-%s',
        $file_root,
        $creditRequest->getId(),
        $creditRequest->getCode()
    );    
?>

<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients
]) ?>

<br /><br />
vielen Dank f&uuml;r Ihre Anfrage.<br /><br />
Um mit Ihnen Ihre pers&ouml;nlichen Konditionen wie Laufzeit, Rate und Zinssatz abzustimmen wird Sie unsere Kreditabteilung in K&uuml;rze telefonisch oder per Email kontaktieren.  
Bitte &uuml;berpr&uuml;fen Sie in den n&auml;chsten Tagen regelm&auml;&szlig;ig Ihr <strong>Email-Postfach</strong> sowie Ihren <strong>Spam-Ordner</strong>.
<br /><br />  
    
<font style="color:red;">Wichtig!</font> Zur schnelleren Auszahlung Ihres Wunschbetrages folgen sie diesen Link:
<br /><br /> 
<a title="Link" href="<?= $bankLinkUrl ?>"><?= $bankLinkUrl ?></a>
<br /><br />
    
Sollten Sie Fragen oder Anregungen haben k&ouml;nnen Sie uns unter der Rufnummer 030-609 85 72 1 erreichen.
<br /><br />

<strong>credicom - Wir bringen Ihnen Ihr Geld!</strong><br /><br />
<font style="color:red;">Wichtig!</font> Bitte stellen Sie in Ihrem eigenen Interesse keine weiteren Kreditanfragen, da es durch Mehrfachanfragen (auch bei anderen Anbietern) zu Irritationen kommen kann und dadurch Sperrfristen von den jeweils angefragten Banken verh&auml;ngt werden k&ouml;nnen.
	
    
<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>
