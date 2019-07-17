<?php
//    $template_path = '/admin/templates/cayou/';

global $file_root;
global $file_root_img_icons;
global $file_root_img;

$meta_title = 'Ratenkredit von credicom - jetzt unverbindlich Wunschbetrag sichern!';
$meta_description = 'Der Ratenkredit von credicom. Individuelle Konditionen f&uuml;r Ihren Wunschkredit. Nat&uuml;rlich ohne Vorkosten - auch bei negativer Schufa! Fragen Sie jetzt an.';
$meta_keywords = '';  
$menu = ''; 

?>

<!DOCTYPE html>
<html class="no-js" lang="de">
<head>
	<?php if($_SERVER['HTTP_HOST']!='localhost') {?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-MQH4D79');</script>
	<!-- End Google Tag Manager -->	
	<?php }?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800">
    <link rel="stylesheet" href="<?=$file_root;?>css/main.css">
    <script src="<?=$file_root;?>scripts/vendor/modernizr.js"></script>  

    <script src="<?=$file_root;?>js/textarea_maxlength.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?=$file_root?>admin/js/datepicker.js"></script>
    <script type="text/javascript" src="<?=$file_root;?>admin/js/datetimepicker-master/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=$file_root;?>admin/js/datetimepicker-master/jquery.datetimepicker.css"/>	
    <script src="<?=$file_root;?>admin/js/datetimepicker-master/jquery.datetimepicker.js"></script>	
  
    <link rel="shortcut icon" href="/inc/favicon.ico" type="image/x-icon" />
  
	<title><?=$meta_title;?></title>
	<meta name="title" content="<?=$meta_title;?>" />
  <meta name="description" content="<?=$meta_description;?>" />
  <meta name="keywords" content="<?=$meta_keywords;?>" />
  <meta name="language" content="de" />
  <meta name="robots" content="INDEX,FOLLOW" />
  <meta name="format-detection" content="telephone=no">

	<meta name="google-site-verification" content="7p1MVKu9MFSzx2OxzL7zGJ1tDry8JZ_ZtRrB3idrxl8" />
</head>
<body class="page">
	<?php if($_SERVER['HTTP_HOST']!='localhost') {?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MQH4D79"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->	
	<?php }?>
  <header class="page-header">
    <div class="page-header-greenbar">
      <div class="container">
        <div class="flex">
        	<span class="hidden-xs">
        		<img src="<?=$file_root_img_icons;?>icon_pfeil.png" alt="" title="">
        		So erreichen Sie uns <strong>direkt</strong>:
        	</span>
        	<span class="glyphicon glyphicon-earphone" aria-hidden="true"></span><strong>030-60985721</strong>
        	<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>&#105;&#110;&#102;&#111;&#064;&#099;&#114;&#101;&#100;&#105;&#099;&#111;&#109;&#046;&#100;&#101;
        </div>
      </div>
    </div>
    <div class="page-header-whitebar">
      <nav class="navbar navbar-default">
        <div class="container">
          <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#navbar" aria-expanded="false"><span class="sr-only">Navigation ein-/ausklappen</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            <div class="navbar-brand">
                <a class="navbar-brand-image" href="<?=$file_root;?>" title=""><img src="<?=$file_root_img;?>logo_header.png" alt="" title=""></a>
            </div>
          </div>
          <div class="collapse navbar-collapse" id="navbar">
            <ul class="nav navbar-nav navbar-right">
            	<!--<li<?if($menu==1){echo ' class="active"';};?>><a href="<?=$file_root;?>" title=""><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>-->
              <li<?if($menu==2){echo ' class="active"';};?>><a href="<?=$file_root;?>ueber-uns.html" title="">&Uuml;ber uns</a></li>
              <li<?if($menu==3){echo ' class="active"';};?>><a href="<?=$file_root;?>faq.html" title="">Fragen &amp; Antworten</a></li>
              <li<?if($menu==4){echo ' class="active"';};?>><a href="<?=$file_root;?>kundenmeinungen.html" title="">Kundenmeinungen</a></li>
              <li>
              	<button class="btn btn-orange navbar-btn text-upper" type="button" onclick="window.location.href='<?=$file_root;?>kreditanfrage.html'">Anfrage<span class="glyphicon glyphicon-menu-right"></span></button>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>

	<?php echo $yield ?>

	<footer class="page-footer">
		<section class="page-footer-widget widget-container" id="widget-last">
			<div class="container">
				<div class="row">
					<div class="col-md-3 col-sm-6">
						<img src="<?=$file_root_img;?>logo_footer.png" alt="" title="" class="pt-lg pb-lg" />
						<div class="row">
						  <div class="col-xs-2"><span class="glyphicon glyphicon-map-marker glyphicon-green"></span></div>
						  <div class="col-xs-10">
						    <p>
							    Credicom GmbH<br />
							    Uhlandstraße 20-25<br />
							    10623 Berlin
						    </p>
						  </div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<p class="pt-md mb-xs text-sm">Rufen Sie uns an:</p>
						<p class="mb-xs text-xxl text-green font-weight-bold font-kursiv"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> 030-60985721</p>
						<p class="text-sm">oder senden Sie uns ein Fax: 030-60985722</p>
						<p class="pt-lg mb-xs text-sm">E-Mails an:</p>
						<p class="text-lg text-green font-weight-bold font-kursiv"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> &#105;&#110;&#102;&#111;&#064;&#099;&#114;&#101;&#100;&#105;&#099;&#111;&#109;&#046;&#100;&#101;</p>						
					</div>	
					<div class="clearfix hidden-lg hidden-md"></div>
					<div class="col-md-1 col-sm-6">
						<ul>
							<li><a href="<?=$file_root;?>kontakt.html" title="">Kontakt</a></li>
							<li><a href="<?=$file_root;?>faq.html" title="">FAQ</a></li>
							<li><a href="<?=$file_root;?>ueber-uns.html" title="">&Uuml;ber uns</a></li>
							<li><a href="<?=$file_root;?>impressum.html" title="">Impressum</a></li>
							<li><a href="<?=$file_root;?>agb.html" title="">AGB</a></li>
							<li><a href="<?=$file_root;?>datenschutz.html" title="">Datenschutz</a></li>
						</ul>
					</div>	
					<div class="col-md-5 col-sm-6">
						<div class="row pt-lg">
							<div class="col-xs-12 col-sm-4">
								<a href="https://www.ekomi.de/bewertungen-credicomde.html" title="" target="_blank"><img src="<?=$file_root_img;?>footer_ekomi.png" alt="" title=""></a>
							</div>
							<div class="col-xs-12 col-sm-4">
							<a href="https://verbraucherschutz.de/credicom-kredite-berlin/" title="" target="_blank"><img src="<?=$file_root_img;?>footer_vschutz.png" alt="" title=""></a>
							</div>
							<div class="col-xs-12 col-sm-4">
								<img src="<?=$file_root_img;?>footer_bcheck.png" alt="" title="">
							</div>
						</div>	
					</div>	
					<div class="clearfix"></div>
					<hr class="weiss mt-xs mb-xl" />
					<a href="<?=$file_root?>sofortkredit.html" title="Sofortkredit" class="btn btn-orange">Sofortkredit</a>	
					<a href="<?=$file_root?>kredit-ohne-schufa.html" title="Kredit ohne Schufa" class="btn btn-orange">Kredit ohne Schufa</a>
					<a href="<?=$file_root?>umschuldung.html" title="Umschuldung" class="btn btn-orange">Umschuldung</a>			
					<a href="<?=$file_root?>kredit-online-beantragen.html" title="Onlinekredit" class="btn btn-orange">Onlinekredit</a>
					<a href="<?=$file_root?>ratenkredit.html" title="Ratenkredit" class="btn btn-orange">Ratenkredit</a>
					<a href="<?=$file_root?>urlaubskredit.html" title="Urlaubskredit" class="btn btn-orange">Urlaubskredit</a>
					<a href="<?=$file_root?>kredit-fuer-selbststaendige.html" title="Kredit f&uuml;r Selbstst&auml;ndige" class="btn btn-orange">Kredit f&uuml;r Selbstst&auml;ndige</a>
					<a href="<?=$file_root?>autokredit.html" title="Autokredit" class="btn btn-orange">Autokredit</a>
					<a href="<?=$file_root?>baufinanzierung.html" title="Baufinanzierung" class="btn btn-orange">Baufinanzierung</a>		          		           		 		
				</div>
			</div>
		</section>
	</footer>
	

	<div class="container">            
		<div class="row">
	  	<div class="col-md-12">		
				<p class="aligncenter text-sm">
					Angaben gem. &sect; 6a PAngV: Unver&auml;nderlicher Sollzinssatz ab 3,44%, effektiver Jahreszins ab 3,49% - 15,95%, Nettodarlehensbetrag ab 1000,- bis 300.000,- &euro;, Laufzeit von 12 bis 120 Monaten, Bonit&auml;t vorausgesetzt. Repr&auml;sentatives Beispiel: Sollzinssatz 5,92% fest f&uuml;r die gesamte Laufzeit, Effektiver Jahreszins: 6,09%, Nettokreditbetrag:  10.000,- &euro;, Vertragslaufzeit: 60 Monate, Monatliche Rate: 193,00 &euro;, Gesamter Zinsaufwand: 1573,98 &euro;, Gesamtr&uuml;ckzahlung (inkl. aller Geb&uuml;hren): 11.576,98 &euro;.
				</p>			
			</div>
		</div>
	</div>	
		
  <script src="<?=$file_root;?>scripts/vendor.js"></script>
  <script src="<?=$file_root;?>scripts/plugins.js"></script>
    <script src="<?=$file_root;?>scripts/creditcalc.js"></script>
    <script src="<?=$file_root;?>scripts/main.js"></script>

  <script src="<?=$file_root;?>scripts/validate/jquery.validate.min.js"></script>
  <script src="<?=$file_root;?>scripts/validate/localization/messages_de.min.js"></script>
<!--test all-->
</body>
</html>
