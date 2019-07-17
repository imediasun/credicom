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
<br />Statuswechsel Kunde <?= $client->getNachname() ?> <?= $client->getVorname() ?>
<br /><a href="<?= $creditRequestUrl ?>">Kreditanfrage - <?= $creditRequest->getId() ?></a>
<br />Bitte den Kunden manuell prüfen siehe Bemerkungsfeld.<br />
