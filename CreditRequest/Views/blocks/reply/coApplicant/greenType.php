<?php	
    global $file_root_img_icons;
?>

<h2 class="mb-xxl"><img src="<?= $file_root_img_icons; ?>text_brand.png" alt="" class="hidden-xs" /> Vielen Dank f&uuml;r die Vervollst&auml;ndigung der Angaben Ihres Mitantragstellers!</h2>

<?= $this->render('creditRequest/blocks/reply/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $reply->availableRecipients
]) ?>

<p>unser Serviceteam wird sich mit Ihnen telefonisch oder per Email in Verbindung setzen, um konkrete Kreditzusagen f&uuml;r Sie seitens unserer Partnerbanken zu erhalten.</p>
<p>Bitte &uuml;berpr&uuml;fen Sie in den n&auml;chsten Tagen regelm&auml;ßig Ihr Email-Postfach sowie Ihren Spam-Ordner.</p> 

