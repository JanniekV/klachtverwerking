<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;

class KlachtMailer
{
    public function verstuur(string $naam, string $email, string $omschrijving): void
    {
        $mail = new PHPMailer(true); // true = gooi exceptions bij fouten

        // Server-instellingen uit .env
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'];
        $mail->Password   = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) $_ENV['MAIL_PORT'];
        $mail->CharSet    = 'UTF-8';

        // Afzender, ontvanger en cc
        $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
        $mail->addAddress($email, $naam);
        $mail->addCC($_ENV['MAIL_CC']);

        // Inhoud
        $veiligNaam  = htmlspecialchars($naam);
        $veiligEmail = htmlspecialchars($email);
        $veiligKlacht = nl2br(htmlspecialchars($omschrijving));

        $mail->isHTML(true);
        $mail->Subject = 'Uw klacht is in behandeling';
        $mail->Body = "
            <p>Beste {$veiligNaam},</p>
            <p>Wij hebben uw klacht ontvangen. Dit zijn de gegevens die u heeft ingevuld:</p>
            <p><strong>Naam:</strong> {$veiligNaam}<br>
            <strong>E-mail:</strong> {$veiligEmail}<br>
            <strong>Omschrijving:</strong><br>{$veiligKlacht}</p>
        ";
        $mail->AltBody = "Naam: {$naam}\nE-mail: {$email}\nOmschrijving:\n{$omschrijving}";

        $mail->send();
    }
}