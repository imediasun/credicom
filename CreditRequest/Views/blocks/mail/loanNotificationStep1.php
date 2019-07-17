<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest
]) ?>

<br /><br />
vielen Dank f&uuml;r Ihre Kreditanfrage!<br /><br />
Wir haben Ihnen heute ein Kreditangebot per Post versandt.<br /><br />
Das Angebot muss in den n&auml;chsten <strong>2-3 Werktagen</strong> bei Ihnen eintreffen - achten Sie auf einen DIN A4-Umschlag in Ihrem Briefkasten.<br /><br />
Bitte melden Sie sich unbedingt, falls das Kreditangebot in diesem Zeitraum nicht zugestellt wurde. Wir werden Ihnen das Kreditangebot neu zusenden.<br /><br />
Wir sind Montag bis Freitag in der Zeit von 09:00 bis 18:00 Uhr unter der <strong>Rufnummer 030-6098 5721</strong> f&uuml;r Sie erreichbar und beantworten gerne Ihre Fragen.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>