<?php

declare(strict_types=1);

require_once __DIR__ . '/necrocon/php-cs-fixer/JanusRules.php';

return (new PhpCsFixer\Config())
    ->setRules(JanusRules::rules())
    ->setFinder(JanusRules::finder(__DIR__))
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
