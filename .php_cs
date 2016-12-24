<?php

require_once __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->exclude(__DIR__ . '/tests/resources');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'no_unused_imports' => true,
        'function_typehint_space' => true,
        'trailing_comma_in_multiline_array' => true,
        'whitespace_after_comma_in_array' => true,
        'concat_space' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'array_syntax' => array('syntax' => 'short'),
    ])
    ->setFinder($finder);
