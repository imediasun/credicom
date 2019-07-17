<?php
    use \CreditRequest\Block\Epost\Base as BaseEpostNotification;
    $availableRecipients = ($availableRecipients) ? $availableRecipients : BaseEpostNotification::AVAILABLE_RECIPIENTS_APPLICANT;
?>

Kundennummer: <?= $client->getId() ?>
<br /><br />

<?= $this->render('creditRequest/blocks/mail/layout/recipients.php', [
    'creditRequest' => $creditRequest,
    'availableRecipients' => $availableRecipients,
]) ?>
