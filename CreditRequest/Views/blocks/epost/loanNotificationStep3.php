<?= $this->render('creditRequest/blocks/epost/layout/header.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

<u><strong>Ihr pers&ouml;nliches Kreditangebot</strong></u><br /><br /><br />

<?= $this->render('creditRequest/blocks/epost/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

vor kurzem haben wir Ihnen Ihren pers&ouml;nlichen Kreditvertrag per Post versandt.<br /><br />
Haben Sie die Kreditunterlagen der Bank erhalten? Gibt es R&uuml;ckfragen zu diesem Angebot oder m&ouml;chten Sie eine &Auml;nderung vornehmen lassen?<br /><br />
Da wir an einer hohen Kundenzufriedenheit interessiert sind, w&auml;re es sehr nett von Ihnen, mir ein Feedback zum Vertrag zu geben.<br /><br />
Wir sind Montag bis Freitag in der Zeit von 09:00 bis 18:00 Uhr unter der <strong>Rufnummer 030-6098 5721</strong> f&uuml;r Sie erreichbar und beantworten gerne Ihre Fragen.

<?= $this->render( 'creditRequest/blocks/epost/layout/footer.php', [
    'sender' => $sender,
]) ?>