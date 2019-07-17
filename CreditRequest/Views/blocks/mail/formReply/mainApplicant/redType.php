<?php
    $config = \Core\Model\Registry::getInstance()->getConfig();
    $affiliateLink = $config['api']['auxmoney']['affiliateLink'];
?> 

<?= $this->render('creditRequest/blocks/mail/layout/header.php', [
    'client' => $client,
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients
]) ?>

<br /><br />
In Ihrem Fall hat unsere automatische Vorpr&uuml;fung ergeben, dass im Moment leider kein Kreditangebot f&uuml;r Sie vorhanden ist.
<br /><br /> 
Dennoch haben wir noch einen Partner f&uuml;r Sie, der auch in Ihrem Fall Geld von Privat zur Verf&uuml;gung stellen kann. Eine Ablehnung ist dort &auml;u&szlig;erst selten. Wir haben bereits tausende Kunden vor Ihnen dort positiv vermitteln k&ouml;nnen.
<br /><br />

<a href="<?= $affiliateLink; ?>" title="">Hier klicken</a><br /><br />
            
Klicken Sie auf den Link und f&uuml;llen Sie dort das Formular vollst&auml;ndig aus. Es entstehen Ihnen keine Kosten und Ihre Anfrage bleibt v&ouml;llig anonym.
<br /><br /> 

<?php if(!$creditRequest->masteller) { ?>
    <strong>Tip:</strong> Mit einem Mitantragsteller bei <strong class="text-green">credicom</strong> erh&ouml;hen Sie immer Ihre Chancen auf eine Kreditzusage!	
<?php } ?>
    

<?= $this->render( 'creditRequest/blocks/mail/layout/footer.php', [
    'sender' => $sender,
    'senderName' => $senderName,
]) ?>

