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
<br />Kunde <?= $client->getNachname() ?> <?= $client->getVorname() ?> hat einen Mitantragsteller f&uuml;r den ein auxmoney Vertrag erstellt wurde.
<br /><a href="<?= $creditRequestUrl ?>">Kreditanfrage - <?= $creditRequest->getId() ?></a>
<br />Bitte den Vertrag ausdrucken und versenden.<br />

