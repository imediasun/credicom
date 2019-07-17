<?= $this->render('creditRequest/blocks/epost/layout/header.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

<u><strong>Ihr pers&ouml;nliches Kreditangebot – Achtung Fristablauf!</strong></u><br /><br /><br />

<?= $this->render('creditRequest/blocks/epost/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>

leider haben Sie auf unsere Bem&uuml;hungen bisher nicht reagiert - dabei k&ouml;nnten Sie bereits &uuml;ber Ihren <strong>Wunschkredit</strong> verf&uuml;gen!<br /><br />
Bitte teilen Sie mir mit, ob Sie noch Interesse an einem Kredit haben.<br /><br />
Sollten wir bis zum <strong><?= date ('d.m.Y' , strtotime("+3 days")) ?></strong> keine R&uuml;ckmeldung von Ihnen erhalten, werden wir Ihren Kreditvertrag in 3 Tagen bei der Bank in Storno stellen.

<?= $this->render( 'creditRequest/blocks/epost/layout/footer.php', [
    'sender' => $sender,
]) ?>