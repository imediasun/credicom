<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients
]) ?>

<br /><br />	
wir haben einen Partner f&uuml;r Ihren Finanzierungswunsch gefunden.
<br /><br />
Bitte laden Sie l&uuml;ckenlos die Kontoausz&uuml;ge der letzten 3 Monate direkt hier &uuml;ber das sichere auxmoney-Formular hoch. Auxmoney Kreditpr&uuml;fer schauen sich Ihre Unterlagen umgehend an und Sie erhalten den Kreditvertrag direkt per E-Mail und per Post.
<br /><br />  
Damit Sie das Geld so schnell wie m&ouml;glich auf Ihrem Konto haben, klicken Sie bitte den folgenden Link:
<br /><br />

<a title="Link" href="<?= $auxmoney->ekf_url; ?>">Hier klicken</a><br /><br />	

Hier haben Sie 3 m&ouml;gliche Optionen:
<br /><br />
1. Kontoausz&uuml;ge online freigeben<br />
- Ihre Kontoausz&uuml;ge werden sofort bearbeitet Sie haben Ihr Geld innerhalb weniger Tage auf Ihrem Konto
<br /><br />
2. Kontoausz&uuml;ge manuell hochladen<br />
- Die Bearbeitung beginnt nach kurzer Zeit und Sie haben Ihr Geld nach ca. 3-4 Tagen auf Ihrem Konto
<br /><br />
3. Kontoausz&uuml;ge per Post schicken<br />
- Die Bearbeitung erfolgt nach Posteingang und Sie haben Ihr Geld nach ca. 7-8 Tagen auf Ihrem Konto
<br /><br />

Wichtig! Die Bearbeitung ist jederzeit kostenlos und unverbindlich f&uuml;r Sie. Nur noch wenige Schritte bis zu Ihrem Geld.
<br /><br />
Sollten Sie R&uuml;ckfragen zur Ihrem Kreditvertrag haben, melden Sie sich gerne unter der kostenfreien Rufnummer: 030 60985721.
<br /><br />					
Vielen Dank f&uuml;r Ihr Vertrauen 
   
<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>


