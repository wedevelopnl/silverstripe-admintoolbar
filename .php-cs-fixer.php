<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
;

$config = new PhpCsFixer\Config();
$config
    ->setRules([
        '@PHP81Migration' => true,
        '@PSR12' => true,
        'array_push' => true,
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'cast_spaces' => ['space' => 'none'],
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;

return $config;