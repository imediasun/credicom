<div class="content" id="content-form">
    
    <section class="widget-container teaser anfrage hidden-xs">
        <div class="container">
            <div class="row">									
                <div class="col-lg-8 col-lg-offset-4">
                    <h1>
                        Einfach Anfrage ausf&uuml;llen und kostenloses unverbindliches Kreditangebot erhalten
                    </h1>
                    <div class="col-sm-6 pr-none pl-none">
                        <ul>
                            <li>30 Jahre Erfahrung im Kreditgesch&auml;ft</li>
                            <li>Sofortkredite ab 1.000,-&euro; bis 300.000,-&euro;</li>
                        </ul>
                    </div>
                    <div class="col-sm-6 pr-none pl-none">
                        <ul>
                            <li>Keine Vorkosten &amp; kostenloses Kreditangebot</li>
                            <li>Kredite auch bei negativer Schufa oder Schulden</li>
                        </ul>										
                    </div>
                </div>
            </div>
        </div>	
    </section>     
    
    <div class="container container-reply">
        <div id="loading-placeholder">
            <img src="<?= $GLOBALS['file_root']?>img/credicom-loading-placeholder.gif" />
            <br /><strong class="text-green">Bitte haben Sie noch einen Moment Geduld</strong>
        </div>
        <div class="row">            
            <div class="col-lg-9" id="reply-wrapper"></div>
        </div>        
    </div>        

</div>


<script>

$(document).ready(function() {  
    $.ajax({
        url: "<?= $GLOBALS['file_root']?>credit-request/co-applicant/get-reply-type",
        type: 'POST',
        data: {
            id : '<?= $id ?>',
            code: '<?= $code ?>',
            item : '<?= json_encode($item) ?>',
        },
        success: function (response) {
            $('#reply-wrapper').html(response);
            $('#loading-placeholder').css('display', 'none');
            $('#content-form > .container').css('height', 'initial');
        }
    });
});

</script>
