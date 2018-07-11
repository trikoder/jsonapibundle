<?php

/** @var \Composer\Autoload\ClassLoader $loader */
if (!($loader = @include __DIR__ . '/../../../vendor/autoload.php')) {
    echo <<<EOT
You need to install the project dependencies using Composer:
  composer install --dev
EOT;
    exit(1);
}
use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
