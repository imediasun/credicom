<?php	
    global $file_root_img_icons;
$contractLink = $GLOBALS["file_root"] . "auxmoney/contract/view/" . $thi->auxmoney['main_applicant'] . "/" . $creditRequest['id'] . "/" . $creditRequest['code'];
?>

<h2 class="mb-xxl"><img src="<?= $file_root_img_icons; ?>text_brand.png" alt="" class="hidden-xs" /> Nur noch wenige Sekunden zu Ihrem Vertrag!</h2>

<?= $this->render('creditRequest/blocks/reply/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $reply->availableRecipients
]) ?>

<p>Sie haben mit credicom die beste Wahl getroffen, denn wir haben den perfekten Partner f&uuml;r Ihren Finanzierungswunsch gefunden.</p>
<p>Ihren pers&ouml;nlichen Vertrag erhalten Sie von unserem Partner auxmoney.</p>
<p class="text-green font-weight-semibold mb-xxl">Nur noch wenige Schritte bis zu Ihrem Geld.</p>
<p class="font-weight-semibold">Drucken Sie den Vertrag einfach direkt aus:</p>
                       
<p class="mb-xl"><a title="Link" href="<?= $contractLink; ?>" target="_blank" class="btn btn-orange">Vertrag &ouml;ffnen <span class="glyphicon glyphicon-menu-right"></span></a></p>
<hr />

<p class="text-green font-weight-semibold">1. Ihre Unterschrift</p>
<p>Unterschreiben Sie den Vertrag an allen markierten Stellen.</p>
<p class="text-green font-weight-semibold mt-lg">2. PostIdent</p>
<p>Legitimieren Sie sich mit dem PostIdent-Verfahren in jeder Deutschen Post Filiale. Nehmen Sie hierzu einfach den kompletten Vertrag und Ihren g&uuml;ltigen Ausweis mit zur Post und senden Sie den Vertrag an unsere Partnerbank.</p>

<hr />

<p class="mb-xl">Sollten Sie R&uuml;ckfragen zur Ihrem Kreditvertrag haben, melden Sie sich gerne unter der kostenfreien Rufnummer: <span class="text-green font-weight-semibold">030 60985721</span>.</p>	
<p><strong><span class="text-green">P.S.:</span> Sie erhalten den Kreditvertrag auch per Post!</strong></p>	

