<?php
/*
if (!@include __DIR__ . '/../vendor/autoload.php') {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}*/

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr0('RestGalleries\\', __DIR__ . '/RestGalleries');

date_default_timezone_set('UTC');
