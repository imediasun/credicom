<?php	
    global $file_root_img_icons;
?>   

<h2 class="mb-xxl"><img src="<?= $file_root_img_icons; ?>text_brand.png" alt="" class="hidden-xs" /> Nur noch wenige Sekunden zu Ihrem Vertrag!</h2>

<?= $this->render('creditRequest/blocks/reply/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $reply->availableRecipients
]) ?>

<p>wir haben einen Partner f&uuml;r Ihren Finanzierungswunsch gefunden.</p>
<p>Bitte laden Sie l&uuml;ckenlos die Kontoausz&uuml;ge der letzten 3 Monate direkt hier &uuml;ber das sichere auxmoney-Formular hoch. Auxmoney Kreditpr&uuml;fer schauen sich Ihre Unterlagen umgehend an und Sie erhalten den Kreditvertrag direkt per E-Mail und per Post.</p>
<p>Damit Sie das Geld so schnell wie m&ouml;glich auf Ihrem Konto haben, klicken Sie bitte den folgenden Link:</p>

<hr />
<p class="mt-xl"><a title="Link" href="<?= $reply->ekf_url; ?>" target="_blank" class="btn btn-orange">Hier klicken <span class="glyphicon glyphicon-menu-right"></span></a></p>
<hr />

<p class="font-weight-semibold mb-xl">Hier haben Sie 3 m&ouml;gliche Optionen:</p>
<p class="text-green font-weight-semibold">1. Kontoausz&uuml;ge freigeben</p>	
<p>- Ihre Kontoausz&uuml;ge werden sofort bearbeitet Sie haben Ihr Geld innerhalb weniger Tage auf Ihrem Konto</p>
<p class="text-green font-weight-semibold">2. Kontoausz&uuml;ge manuell hochladen</p>	
<p>- Die Bearbeitung beginnt nach kurzer Zeit und Sie haben Ihr Geld nach ca. 3-4 Tagen auf Ihrem Konto</p>	
<p class="text-green font-weight-semibold">3. Kontoausz&uuml;ge per Post schicken</p>	
<p>- Die Bearbeitung erfolgt nach Posteingang und Sie haben Ihr Geld nach ca. 7-8 Tagen auf Ihrem Konto</p>	

<hr />

<p><span class="text-green font-weight-semibold">Wichtig!</span> Die Bearbeitung ist jederzeit kostenlos und unverbindlich f&uuml;r Sie. Nur noch wenige Schritte bis zu Ihrem Geld.</p>				
<p>Sollten Sie R&uuml;ckfragen zur Ihrem Kreditvertrag haben, melden Sie sich gerne unter der kostenfreien Rufnummer: <span class="text-green font-weight-semibold">030 60985721</span>.</p>			


