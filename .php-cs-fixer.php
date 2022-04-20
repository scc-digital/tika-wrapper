<?php

declare(strict_types=1);

/*
 * This file is part of the Zapoyok project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$header = <<<'HEADER'
    This file is part of the Zapoyok project.

    (c) Jérôme Fix <jerome.fix@zapoyok.info>

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
    HEADER;

$finder = PhpCsFixer\Finder::create()
    ->in('src/')
;

$config = new PhpCsFixer\Config();

    return $config
        ->setRiskyAllowed(true)
        ->setRules([
            '@PHP71Migration' => true,
            '@PHP71Migration:risky' => true,
            '@PHP73Migration' => true,
            '@PhpCsFixer' => true,
            '@Symfony' => true,
            '@Symfony:risky' => true,
            '@DoctrineAnnotation' => true,
            'array_syntax' => ['syntax' => 'short'],
            'general_phpdoc_annotation_remove' => ['annotations' => ['author', 'package', 'subpackage']],
            'no_useless_return' => true,
            'phpdoc_to_comment' => false,
            'method_chaining_indentation' => false,
            'indentation_type' => true,
            'ordered_imports' => true,
            'line_ending' => true,
            'no_superfluous_phpdoc_tags' => true,
            'concat_space' => ['spacing' => 'one'],
            'class_definition' => [
                'multi_line_extends_each_single_line' => true,
                'single_item_single_line' => false,
                'single_line' => false,
            ],
            'php_unit_test_class_requires_covers' => false,
            'phpdoc_order' => true,
            'phpdoc_align' => ['align' => 'vertical'],
            'self_accessor' => false,
            'header_comment' => ['header' => $header, 'comment_type' => 'comment', 'location' => 'after_declare_strict'],
        ])
        ->setUsingCache(true)
        ->setFinder($finder)
    ;
