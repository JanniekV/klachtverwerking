<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class KlachtLogger
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('klachten');

        // Schrijf alles vanaf niveau "info" naar logs/info.log
        $this->logger->pushHandler(
            new StreamHandler(__DIR__ . '/../logs/info.log', Level::Info)
        );
    }

    public function logKlacht(string $naam, string $email, string $omschrijving): void
    {
        $this->logger->info('Nieuwe klacht ontvangen', [
            'naam' => $naam,
            'email' => $email,
            'omschrijving' => $omschrijving,
        ]);
    }

    public function logFout(string $melding): void
    {
        $this->logger->error('E-mail versturen mislukt', ['fout' => $melding]);
    }
}
