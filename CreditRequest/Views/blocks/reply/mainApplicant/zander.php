<?php	
    global $file_root_img_icons;
?>   

<h2 class="mb-xxl"><img src="<?= $file_root_img_icons; ?>text_brand.png" alt="" class="hidden-xs" /> Vielen Dank f&uuml;r Ihre Anfrage bei credicom!</h2>

<?= $this->render('creditRequest/blocks/reply/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $reply->availableRecipients
]) ?>

<p>Nach positiver Vorpr&uuml;fung wird Ihre Anfrage an unseren sehr erfolgreichen Partner weitergeleitet:</p>

<br />

<p>FVZ GmbH</p>
<p>Rudolf-Walther-Str. 5</p>
<p>06188 Landsberg</p>
<p>Internet: www.credit12.de</p>

<br />

<p>Sie werden umgehend von unserem Partner zur schnellen und erfolgreichen Bearbeitung Ihres Kreditwunsches kontaktiert - nat&uuml;rlich kostenfrei und unverbindlich!</p>

<br />
<p><span class="text-green font-weight-semibold">Mit freundlichem Gruß<br />credicom Team</span></p>
