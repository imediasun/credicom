<?php
    global $file_root;
    global $file_roots;
    global $file_root_img;	
    global $file_root_img_icons;
?>

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
    
    <div class="container">
        <h1 class="mb-sm">Daten des Mitantragsteller</h1>
        <div class="row">
            <div class="col-lg-9">
                <?= $form->render() ?>
            </div>
            <div class="col-lg-3 text-center">
                <div class="box-gray rounded pt-md pb-md pl-sm pr-sm">
                    <img src="<?=$file_root_img;?>ekomi_siegel.png" alt="" class="mb-sm" /><br />
                    <img src="<?=$file_root_img;?>ekomi_sterne.png" alt="" class="mb-sm" /><br />
                    <span class="font-kursiv text-hgray text-sm">Durchschnitt aus 92 Bewertungen der letzten 12 Monate</span>
                </div>
                <div class="box-gray rounded pt-md pb-md pl-sm pr-sm mt-xl">
                    <img src="<?=$file_root_img;?>bankingcheck.png" alt="" class="mb-sm" /><br />
                    <span class="font-kursiv text-sm">BackingCheck.de Test + Kategorie Vermittler:</span><br />
                    <span class="text-green font-weight-bold text-md">"Beste Bank / Anbieter und bestes Finanzprodukt 2017"</span>
                </div>	
                <div class="box-green rounded pt-md pb-md pl-sm pr-sm mt-xl">
                    <span class="font-weight-bold font-kursiv text-xl">Haben Sie Fragen?</span><br />
                    <p class="text-center mt-md mb-md">Rufen Sie einfach unsere<br /> Service-Hotline an:</p>
                    <span class="font-weight-bold font-kursiv text-xl"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> 030-60985721</span>
                </div>																	

                <img src="<?=$file_root_img_icons;?>daumen.png" alt="" class="mt-xl" />
            </div>
        </div>
    </div>        

</div>