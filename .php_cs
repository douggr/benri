<?php

// All options available at https://github.com/FriendsOfPHP/PHP-CS-Fixer
$includes = [
    'align_double_arrow',
    'align_equals',
    'concat_with_spaces',
    'newline_after_open_tag',
    'ordered_use',
    'phpdoc_no_access',
    'phpdoc_no_empty_return',
    'phpdoc_no_package',
    'phpdoc_scalar',
    'psr-0',
    'short_array_syntax',
    'short_echo_tag',
    'strict_param',
    'strict',
];

$excludes = [
    'concat_without_spaces',
    'concat_without_spaces',
    'extra_empty_lines',
    'phpdoc_indent',
    'phpdoc_no_access',
    'phpdoc_params',
    'phpdoc_separation',
    'trim_array_spaces',
    'unalign_double_arrow',
    'unalign_equals',
];


array_walk($excludes, function (&$item) {
    $item = "-{$item}";
});


return Symfony\CS\Config\Config
    ::create()
    ->fixers(array_merge($includes, $excludes))
    ->setUsingCache(true)
    ->setUsingLinter(true)
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
        ->in([
            'library',
        ])
    );
