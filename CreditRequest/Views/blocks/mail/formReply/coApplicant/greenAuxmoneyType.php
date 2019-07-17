<?php 
    $isMainApplicant = false;
    $contractLink = $GLOBALS["file_root"] . "auxmoney/contract/view/" . (($isMainApplicant) ? 1 : 0) . "/" . $creditRequest['id'] . "/" . $creditRequest['code'];
?>

<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients
]) ?>

<br /><br />
Sie haben mit credicom die beste Wahl getroffen, denn wir haben den perfekten Partner f&uuml;r Ihren Finanzierungswunsch gefunden.
<br /><br />	
Ihren pers&ouml;nlichen Vertrag erhalten Sie von unserem Partner auxmoney.
<br /><br />	
Nur noch wenige Schritte bis zu Ihrem Geld.
<br /><br />

<hr />
Drucken Sie den Vertrag einfach direkt aus:
<br /><br />
<a title="Link" href="<?= $contractLink; ?>">Vertrag &ouml;ffnen</a>
<hr />
<br />

1. Ihre Unterschrift<br /><br />
Unterschreiben Sie den Vertrag an allen markierten Stellen.<br /><br />
2. PostIdent<br /><br />
Legitimieren Sie sich mit dem PostIdent-Verfahren in jeder Deutschen Post Filiale. Nehmen Sie hierzu einfach den kompletten Vertrag und Ihren g&uuml;ltigen Ausweis mit zur Post und senden Sie den Vertrag an unsere Partnerbank.<br /><br />
<hr />

Sollten Sie R&uuml;ckfragen zur Ihrem Kreditvertrag haben, melden Sie sich gerne unter der kostenfreien Rufnummer: 030 60985721.
<br /><br />
<strong>P.S.: Sie erhalten den Kreditvertrag auch per Post!</strong>

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>



