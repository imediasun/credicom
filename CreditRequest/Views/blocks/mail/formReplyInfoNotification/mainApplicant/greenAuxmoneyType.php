<?php
    global $file_root_admin;
    $creditRequestUrl = sprintf(
        '%sindex.php?module=Kreditanfragen&method=edit&id=%d&modid=7',
        $file_root_admin,
        $creditRequest->getId()
    );
?>
Kreditanfrage ID: <?= $creditRequest->getId() ?>
<br />
<br />Hallo,
<br />f&uuml;r den Kunden <?= $client->getNachname() ?> <?= $client->getVorname() ?> wurde ein auxmoney Vertrag erstellt.
<br /><a href="<?= $creditRequestUrl ?>">Kreditanfrage - <?= $creditRequest->getId() ?></a>
<br />Bitte den Vertrag ausdrucken und versenden.<br />

