<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest
]) ?>

<br /><br />
vor kurzem haben wir Ihren pers&ouml;nlichen Kreditvertrag per Post versandt.<br /><br />
Haben Sie die Kreditunterlagen der Bank erhalten? Gibt es <strong>R&uuml;ckfragen</strong> zu diesem Angebot oder m&ouml;chten Sie eine &Auml;nderung vornehmen lassen?<br /><br />
Da wir an einer hohen Kundenzufriedenheit interessiert sind, w&auml;re es sehr nett von Ihnen, uns ein Feedback zum Vertrag zu geben.<br /><br />
Wir sind Montag bis Freitag in der Zeit von 09:00 bis 18:00 Uhr unter der <strong>Rufnummer 030-6098 5721</strong> f&uuml;r Sie erreichbar und beantworten gerne Ihre Fragen.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>