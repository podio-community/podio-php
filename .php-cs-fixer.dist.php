<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR2' => true,
];

$finder = Finder::create()->in(__DIR__);

return (new Config())->setRules($rules)->setFinder($finder);
