<?php	
    global $file_root_img_icons;
?>

<h2 class="mb-xxl"><img src="<?= $file_root_img_icons; ?>text_brand.png" alt="" class="hidden-xs" /> Herzlich Willkommen<br /> bei credicom und vielen Dank f&uuml;r Ihre Anfrage!</h2>

<?= $this->render('creditRequest/blocks/reply/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $reply->availableRecipients
]) ?>

<p>unser Serviceteam wird sich mit Ihnen telefonisch oder per Email in Verbindung setzen, um konkrete Kreditzusagen f&uuml;r Sie seitens unserer Partnerbanken zu erhalten.</p>
<p>Bitte &uuml;berpr&uuml;fen Sie in den n&auml;chsten Tagen regelm&auml;ßig Ihr Email-Postfach sowie Ihren Spam-Ordner.</p> 


<div class="request-form">
    <div class="with_shadow">
        <div class="tab-content">
                                    
            <div class="col-sm-2 hidden-xs">
                <img src="<?= $file_root_img_icons; ?>icon_person.png" alt="" class="mr-sm" />
            </div>					
            <div class="col-sm-10 hidden-xs">                                                    
                <table class="table mb-none" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width:80px;" class="text-sm">Name:</td>
                        <td><?= $creditRequest['nachname']; ?></td>
                        <td></td>
                        <td style="width:120px;" class="text-sm">Kundennummer:</td>
                        <td><?= $creditRequest['kid']; ?></td>																								
                    </tr>
                    <tr>
                        <td class="text-sm">Vorname:</td>
                        <td><?= $creditRequest['vorname']; ?></td>
                        <td></td>
                        <td class="text-sm">Antragsnummer:</td>
                        <td><?= $creditRequest['id']; ?></td>																								
                    </tr>
                </table>
            </div>
        
            <div class="clearfix hidden-xs"></div> 

            <div class="col-sm-12">
                <hr class="hidden-xs" />
                <p class="hide-on-valid-form">
                    <span class="text-orange font-weight-bold">Wichtig!</span><br />
                    <span class="text-green font-weight-bold">Zur schnelleren Auszahlung Ihres Wunschbetrages geben Sie bitte hier Ihre Bankverbindung ein!</span>
                </p>

                <div class="tab-pane active">
                    <form method="POST" id="content_form">
                        <input type="hidden" name="item[id]" value="<?= $creditRequest['id']; ?>">
                        <input type="hidden" name="item[code]" value="<?= $creditRequest['code']; ?>">
                        <div class="request-form-pane">
                            <div class="error-container mr-none ml-none"></div>
                            <div class="success-container mr-none ml-none"></div>
                            <div class="hide-on-valid-form einruecken mt-xl">
                                <?= $form->render(); ?>

                                <div class="col-sm-12 form-group text-right">

                                  <button class="btn btn-orange btn-goon creditform-edit-submit">
                                      speichern<span class="glyphicon glyphicon-menu-right"></span>
                                  </button>

                                </div>


                            </div>
                        </div>

                    </form>
                    <script>
                        var creditFormUrls = <?= json_encode($creditFormUrls); ?>;
                    </script>
                </div>
            </div>       
            <div class="clearfix"></div> 
        </div>
    </div>
  </div>

<p>
    Der g&uuml;nstigste Kredit wird Ihnen pers&ouml;nlich per Post zugestellt. Nach Einreichung aller erforderlichen Unterlagen werden diese von unseren Spezialisten gepr&uuml;ft und den Banken &uuml;bergeben. 
    Entsprechen die eingereichten Unterlagen nicht den Anforderungen dieser Bank, wird Ihr pers&ouml;nlicher Ansprechpartner mit Ihnen die bestehenden M&ouml;glichkeiten besprechen.
</p>	

<?php if(!$creditRequest->masteller) { ?>
    <p><strong>Tip:</strong> Mit einem Mitantragsteller bei <strong class="text-green">credicom</strong> erh&ouml;hen Sie immer Ihre Chancen auf eine Kreditzusage!</p>	
<?php } ?>


<h5 class="font-weight-bold">credicom - Wir bringen Ihnen Ihr Geld!</h5>
