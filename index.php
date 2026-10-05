<?php

require __DIR__ . '/vendor/autoload.php';

use App\KlachtMailer;
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\Exception;
use App\KlachtLogger;

// .env inladen
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$melding = '';
$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $omschrijving = trim($_POST['omschrijving'] ?? '');

    if ($naam === '' || $email === '' || $omschrijving === '') {
        $fout = 'Vul alle velden in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fout = 'Vul een geldig e-mailadres in.';
    } else {
        $logger = new KlachtLogger();
        $logger->logKlacht($naam, $email, $omschrijving);
        try {
            (new KlachtMailer())->verstuur($naam, $email, $omschrijving);
            $melding = 'Bedankt! Uw klacht is ontvangen. U krijgt een bevestiging per e-mail.';
        } catch (Exception $e) {
            $fout = 'De e-mail kon niet worden verstuurd: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klachtenformulier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Klacht indienen</h1>

        <?php if ($melding): ?>
            <p class="melding"><?= $melding ?></p>
        <?php endif; ?>

        <?php if ($fout): ?>
            <p class="fout"><?= $fout ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="naam">Naam</label>
            <input type="text" id="naam" name="naam" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="omschrijving">Omschrijving klacht</label>
            <textarea id="omschrijving" name="omschrijving" required></textarea>

            <button type="submit">Versturen</button>
        </form>
    </div>
</body>
</html>