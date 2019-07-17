<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest
]) ?>

<br /><br />
leider haben Sie auf unsere Bem&uuml;hungen bisher nicht reagiert - dabei k&ouml;nnten Sie bereits &uuml;ber Ihren <strong>Wunschkredit</strong> verf&uuml;gen!<br /><br />
Bitte teilen Sie mir mit, ob Sie noch Interesse an einem Kredit haben.<br /><br />
Sollten wir bis zum <strong><?= date ('d.m.Y' , strtotime("+3 days")) ?></strong> keine R&uuml;ckmeldung von Ihnen erhalten, werden wir Ihren Kreditvertrag in 3 Tagen bei der Bank in Storno stellen.

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>