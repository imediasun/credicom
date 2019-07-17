<?php
$header = '';
$array_status_kreditanfragen = [];
$array_cal = [];
$array_banken_neu = [];
function createCmsMenu($data) {return '';}

$template_path = '/admin/templates/cayou/';
$file_root = '/';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Y-CMS | The future of administration</title>
        <script src="<?=$file_root;?>admin/js/textarea_maxlength.js" type="text/javascript"></script>
        <link href="<?=$template_path?>css/mainstyle.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?=$file_root?>admin/js/datepicker.js"></script>
        <script type="text/javascript" src="<?=$file_root;?>admin/js/datetimepicker-master/jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="<?=$file_root;?>admin/js/datetimepicker-master/jquery.datetimepicker.css"/>
        <script src="<?=$file_root;?>admin/js/datetimepicker-master/jquery.datetimepicker.js"></script>
        <link rel="shortcut icon" href="<?=$template_path?>favicon.ico" type="image/x-icon" />


        <!-- jQuery UI -->
<!--        <link rel="stylesheet" href="/application/assets/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css">-->
<!--        <link rel="stylesheet" href="/application/assets/vendor/jquery-ui/themes/flick/jquery-ui.min.css">-->
        <link rel="stylesheet" href="/application/assets/vendor/jquery-ui/themes/blitzer/jquery-ui.min.css">
        <script src="/application/assets/vendor/jquery-ui/ui/minified/jquery-ui.min.js"></script>

        <!-- https://github.com/free-jqgrid/jqGrid -->
        <link rel="stylesheet" href="/application/assets/js/jqGrid/css/ui.jqgrid.min.css">
        <script src="/application/assets/js/jqGrid/i18n/min/grid.locale-de.js"></script>
        <script src="/application/assets/js/jqGrid/jquery.jqgrid.min.js"></script>

    <?=$header?>
    <?php
    foreach($array_status_kreditanfragen as $key=>$value) {
        if($value['cal']==1) {
            $array_cal[]=$value['id'];
        }
    }
    $anzahl_cal=1;
    foreach($array_cal as $key=>$value) {
        $kalender_ids.='val=='.$value;
        if($anzahl_cal<count($array_cal)) {
            $kalender_ids.=' || ';
        }
        $anzahl_cal++;
    }
    ?>
        <script type="text/javascript">
            function setVisibility() {
                var val = document.getElementById('status_intern').value;
                divDaten = document.getElementById('daten');
                if (<?= !empty($kalender_ids) ? $kalender_ids : 'false' ?>) {
                    divDaten.style.display = 'block';
                } else {
                    divDaten.style.display = 'none';
                }
            }
        </script>
        <script type="text/javascript" src="<?=$file_root;?>admin/js/script.js"></script>

        <style type="text/css">
                @media print {
                        .noprint{ display:none; }
                }
        </style>
        <script language="javascript">
            function select(){
                selected = document.pdfkat2.bank.value;
                switch(selected){

                <?php
                    foreach($array_banken_neu as $key=>$value) {
                        echo "
					case '".$value['id']."':
					childx = \"<select name='doku'>";
                        foreach($value['dokumente'] as $key1=>$value1) {
                            echo "<option value='".$value1."'>";
                            foreach($array_schreiben_neu as $key2=>$value2) {
                                if($key2==$value1) {echo $value2['bez'];}
                            }
                            echo "</option>";
                        }
                        echo "</select>\";break;
					";
                    }
                    ?>

                    default:
                        childx = "<select name='doku'><option value=''>---</option></select>";
                        break;
                }

                document.getElementById("dropdown_child").innerHTML = childx;
            }
            if(window.setBallon == null) setBallon = function() {};
        </script>
</head>
<body onload="setBallon('BallonTip');">
<div class="header">
        <a href="./"><img src="<?=$template_path?>images/header.jpg" alt="" /></a>
</div>

<?= new \Admin\Block\Menu\Main() ?>

<div id="content">
    <?= $yield ?>
</div>
<div style="clear:both"></div>

<script type="text/javascript">
    window._mfq = window._mfq || [];
    (function() {
        var mf = document.createElement("script");
        mf.type = "text/javascript"; mf.async = true;
        mf.src = "//cdn.mouseflow.com/projects/682c2373-470a-4d86-a78a-f9ff9cb891a4.js";
        document.getElementsByTagName("head")[0].appendChild(mf);
    })();
</script>

</body>
</html>