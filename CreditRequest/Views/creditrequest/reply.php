<div class="content" id="content-form">
    
    <!--<section class="widget-container teaser anfrage hidden-xs">-->
    <section class="widget-container teaser anfrage">
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

<!-- Google Code for Kreditanfrage (neues Formular) Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 959299503;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "N1qFCMOE3nQQr_-2yQM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/959299503/?label=N1qFCMOE3nQQr_-2yQM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<!-- Facebook Conversion Code for credicom -->
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6022727272764', {'value':'0.01','currency':'EUR'}]);


jQuery(document).ready(function() {
    $.ajax({
        url: "<?= $GLOBALS['file_root']?>credit-request/credit-request/get-reply-type",
        type: 'POST',
        success: function (response) {
            document.cookie = 'credit-form-id' + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            
            $('#reply-wrapper').html(response);
            $('#loading-placeholder').css('display', 'none');
            $('#content-form > .container').css('height', 'initial');
            $('.toggle-control').trigger('change');
        }
    });
});

</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6022727272764&amp;cd[value]=0.01&amp;cd[currency]=EUR&amp;noscript=1" /></noscript>
