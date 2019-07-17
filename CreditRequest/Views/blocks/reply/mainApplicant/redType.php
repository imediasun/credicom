<?php
    global $file_root_img_icons;

    $config = \Core\Model\Registry::getInstance()->getConfig();
    $affiliateLink = $config['api']['auxmoney']['affiliateLink'];
?>   

<h2 class="mb-xxl"><img src="<?= $file_root_img_icons; ?>text_brand.png" alt="" class="hidden-xs" /> Vielen Dank f&uuml;r Ihre Anfrage bei credicom!</h2>

<?= $this->render('creditRequest/blocks/reply/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $reply->availableRecipients
]) ?>

<p>In Ihrem Fall hat unsere automatische Vorpr&uuml;fung ergeben, dass im Moment leider kein Kreditangebot f&uuml;r Sie vorhanden ist.</p>
<p>Dennoch haben wir noch einen Partner f&uuml;r Sie, der auch in Ihrem Fall Geld von Privat zur Verf&uuml;gung stellen kann. Eine Ablehnung ist dort &auml;u&szlig;erst selten. Wir haben bereits tausende Kunden vor Ihnen dort positiv vermitteln k&ouml;nnen.</p>			

<p class="mt-xxl mb-xxl">
    <a href="<?= $affiliateLink; ?>" title="" target="_blank" class="btn btn-orange pull-left mr-xl mb-xl">Hier klicken <span class="glyphicon glyphicon-menu-right"></span></a>
    Klicken Sie auf den Link und f&uuml;llen Sie dort das Formular vollst&auml;ndig aus. Es entstehen Ihnen keine Kosten und Ihre Anfrage bleibt v&ouml;llig anonym.
</p>

<?php if(!$creditRequest->getMasteller()) { ?>
    <p><strong>Tip:</strong> Mit einem Mitantragsteller bei <strong class="text-green">credicom</strong> erh&ouml;hen Sie immer Ihre Chancen auf eine Kreditzusage!</p>	
<?php } ?>



