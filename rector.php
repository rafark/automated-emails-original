<?php

use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {

    require_once '/Applications/MAMP/htdocs/automated-emails/wp-content/plugins/automated-emails/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';

    $rectorConfig->sets([
        DowngradeLevelSetList::DOWN_TO_PHP_72
    ]);

    $rectorConfig->paths([
        './*',
    ]);

    $rectorConfig->skip([
        './vendor/**/*',
        './tests/**/*',
        './bin/**/*',
        './storage/**/*',
        './automated-emails.php'
    ]);

};