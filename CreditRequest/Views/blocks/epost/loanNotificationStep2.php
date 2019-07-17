<?= $this->render('creditRequest/blocks/epost/layout/header.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

<u><strong>Ihr pers&ouml;nliches Kreditangebot</strong></u><br /><br /><br />

<?= $this->render('creditRequest/blocks/epost/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

vor einigen Tagen haben wir Ihnen Ihren pers&ouml;nlichen Kreditvertrag per Post versandt.<br /><br />
F&uuml;r eine <strong>schnelle Auszahlung</strong> und Einl&ouml;sung Ihres <strong>Zalando-Gutscheins</strong> schicken Sie den Kreditvertrag sp&auml;testens morgen zur&uuml;ck.<br /><br />
Je schneller die Dokumente der Bank vorliegen, desto schneller erfolgt die Auszahlung Ihres Wunschkredites!<br /><br />
Wir sind Montag bis Freitag in der Zeit von 09:00 bis 18:00 Uhr unter der <strong>Rufnummer 030-6098 5721</strong> f&uuml;r Sie erreichbar und beantworten gerne Ihre Fragen.

<?= $this->render( 'creditRequest/blocks/epost/layout/footer.php', [
    'sender' => $sender,
]) ?>