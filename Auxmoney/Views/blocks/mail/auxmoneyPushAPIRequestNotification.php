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

<br />Kunde: <?= $client->getNachname() ?> <?= $client->getVorname() ?>
<br />Auxmoney: <?= $notification ?>

<br /><a href="<?= $creditRequestUrl ?>">Kreditanfrage - <?= $creditRequest->getId() ?></a>
<br />Bitte die Anfrage bearbeiten. <br />
